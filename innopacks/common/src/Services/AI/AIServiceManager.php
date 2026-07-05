<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Services\AI;

use Illuminate\Support\Facades\Log;
use Laravel\Ai\AnonymousAgent;
use Laravel\Ai\Messages\Message;

/**
 * Thin facade over the laravel/ai SDK. Plugin code (SitePilot, ContentAI,
 * CustomerService etc.) talks to AIServiceManager so we can evolve the
 * underlying SDK without touching call sites.
 *
 * Configuration flows: settings table -> ProviderRegistry::buildLaravelAiConfig()
 * -> config('ai.*') -> laravel/ai SDK. Boot is done in CommonServiceProvider.
 */
class AIServiceManager
{
    private static ?AIServiceManager $instance = null;

    private array $config;

    private function __construct()
    {
        $this->config = $this->loadConfig();
    }

    public static function getInstance(): AIServiceManager
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Single-shot text generation. Backed by AnonymousAgent.
     */
    public function generate(string $prompt, ?string $purpose = null, array $options = []): string
    {
        $messages = [
            ['role' => 'system', 'content' => $options['system_prompt'] ?? 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        return $this->chat($messages, $options);
    }

    /**
     * Stream text generation.
     */
    public function stream(string $prompt, ?string $purpose = null, array $options = []): iterable
    {
        $system = $options['system_prompt'] ?? 'You are a helpful assistant.';
        $agent  = new AnonymousAgent(
            instructions: $system,
            messages: [new Message('user', $prompt)],
            tools: [],
        );

        foreach ($agent->stream($prompt) as $chunk) {
            yield $chunk;
        }
    }

    /**
     * Multi-turn chat. Splits the system message out for AnonymousAgent
     * (its `instructions` slot), passes the rest as Conversation messages.
     */
    public function chat(array $messages, array $options = []): string
    {
        $system = $options['system_prompt']
            ?? collect($messages)->firstWhere('role', 'system')['content']
            ?? 'You are a helpful assistant.';

        $convoMessages = collect($messages)
            ->reject(fn ($m) => ($m['role'] ?? '') === 'system')
            ->map(fn ($m) => new Message($m['role'], $m['content']))
            ->values()
            ->all();

        $agent = new AnonymousAgent(
            instructions: $system,
            messages: $convoMessages,
            tools: [],
        );

        $lastUser = collect($messages)->last(fn ($m) => ($m['role'] ?? '') === 'user');
        $prompt   = $lastUser['content'] ?? '';

        try {
            $response = $agent->prompt($prompt);

            return $response->text ?? '';
        } catch (\Throwable $e) {
            Log::error('AIServiceManager chat failed: '.$e->getMessage());

            throw new \RuntimeException('AI chat failed: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Multi-turn streaming chat.
     */
    public function chatStream(array $messages, array $options = []): iterable
    {
        $system = $options['system_prompt']
            ?? collect($messages)->firstWhere('role', 'system')['content']
            ?? 'You are a helpful assistant.';

        $convoMessages = collect($messages)
            ->reject(fn ($m) => ($m['role'] ?? '') === 'system')
            ->map(fn ($m) => new Message($m['role'], $m['content']))
            ->values()
            ->all();

        $agent = new AnonymousAgent(
            instructions: $system,
            messages: $convoMessages,
            tools: [],
        );

        $lastUser = collect($messages)->last(fn ($m) => ($m['role'] ?? '') === 'user');
        $prompt   = $lastUser['content'] ?? '';

        foreach ($agent->stream($prompt) as $chunk) {
            yield $chunk;
        }
    }

    /**
     * Backwards-compatible factory. Always returns the SDK-backed service;
     * the model argument is kept for signature compatibility.
     */
    public function make(string $model, array $config = []): AIServiceInterface
    {
        return new class($model, $config) implements AIServiceInterface
        {
            public function __construct(private string $model, private array $config) {}

            public function generate(string $prompt, array $options = []): string
            {
                return AIServiceManager::getInstance()->generate($prompt, null, $options);
            }

            public function stream(string $prompt, array $options = []): iterable
            {
                return AIServiceManager::getInstance()->stream($prompt, null, $options);
            }

            public function validateConfig(array $config): bool
            {
                return ! empty($config['api_key']) || ! empty($config['key']);
            }

            public static function getModelInfo(): array
            {
                return ['name' => 'laravel/ai'];
            }

            public function chat(array $messages, array $options = []): string
            {
                return AIServiceManager::getInstance()->chat($messages, $options);
            }

            public function chatStream(array $messages, array $options = []): iterable
            {
                return AIServiceManager::getInstance()->chatStream($messages, $options);
            }
        };
    }

    /**
     * Resolve which provider code to use for a given purpose.
     */
    public function getModelForPurpose(?string $purpose): string
    {
        $userDefault = system_setting('ai_text_provider') ?: system_setting('ai_model');

        if ($userDefault) {
            return $userDefault;
        }

        return $this->config['default_model'] ?? 'glm';
    }

    /**
     * All available providers from ProviderRegistry presets + user settings.
     */
    public function getAvailableModels(): array
    {
        $result   = [];
        $registry = app(ProviderRegistry::class);

        foreach ($registry->getProviders() as $provider) {
            $code = $provider['code'] ?? '';
            if (! $code) {
                continue;
            }
            $result[$code] = [
                'name'        => $provider['name'] ?? ucfirst($code),
                'driver'      => $provider['driver'] ?? 'openai',
                'base_url'    => $provider['base_url'] ?? '',
                'models'      => $provider['models'] ?? [],
                'description' => $provider['description'] ?? '',
            ];
        }

        return apply_filters('ai.available_models', $result);
    }

    /**
     * Providers that have an API key configured — for panel select dropdowns.
     */
    public function getModelsForSelect(): array
    {
        return app(ProviderRegistry::class)->getConfiguredProviders();
    }

    /**
     * Validate a provider configuration.
     */
    public function validateModelConfig(string $model, array $config): bool
    {
        return ! empty($config['api_key']) || ! empty($config['key']);
    }

    /**
     * All providers that have an API key set (alias for clarity in older callers).
     */
    public function getEnabledModels(): array
    {
        $result = [];
        foreach (app(ProviderRegistry::class)->getProviders() as $provider) {
            if (! empty($provider['api_key'])) {
                $result[$provider['code']] = $provider;
            }
        }

        return $result;
    }

    public function isModelEnabled(string $model): bool
    {
        foreach (app(ProviderRegistry::class)->getProviders() as $provider) {
            if (($provider['code'] ?? '') === $model && ! empty($provider['api_key'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Reload settings → config('ai.*'). Call after settings are mutated in panel.
     */
    public function reloadConfig(): void
    {
        app(ProviderRegistry::class)->buildLaravelAiConfig();
    }

    private function loadConfig(): array
    {
        return [
            'default_model' => config('ai.default', 'glm'),
        ];
    }
}
