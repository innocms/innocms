<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Services\AI;

class ProviderRegistry
{
    /**
     * Built-in provider presets for "Add Provider" UI.
     * Ordered by popularity.
     *
     * Driver notes: laravel/ai's `openai` driver hits the new `/responses`
     * endpoint that only OpenAI itself serves. Every other "OpenAI-compatible"
     * provider (GLM, DeepSeek, Gemini's compat layer, Qianwen, Kimi, Doubao,
     * Hunyuan, MiniMax) only implements `/chat/completions`, which the SDK
     * exposes via the `deepseek` driver. So we set driver='deepseek' for all
     * of them — the `deepseek` driver is essentially "openai-compat via
     * chat/completions" and accepts a custom `url`.
     */
    public static array $presets = [
        'openai' => [
            'name'     => 'OpenAI',
            'driver'   => 'openai',
            'base_url' => 'https://api.openai.com/v1',
            'logo'     => '/images/ai/openai.svg',
            'models'   => [
                'text'  => 'gpt-4o',
                'image' => 'gpt-image-1',
            ],
        ],
        'anthropic' => [
            'name'     => 'Anthropic (Claude)',
            'driver'   => 'anthropic',
            'base_url' => 'https://api.anthropic.com',
            'logo'     => '/images/ai/anthropic.svg',
            'models'   => [
                'text' => 'claude-sonnet-4-6',
            ],
        ],
        'gemini' => [
            'name'     => 'Google Gemini',
            'driver'   => 'deepseek',
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta/openai',
            'logo'     => '/images/ai/gemini.svg',
            'models'   => [
                'text'  => 'gemini-2.5-flash',
                'image' => 'gemini-2.0-flash',
            ],
        ],
        'deepseek' => [
            'name'     => 'DeepSeek',
            'driver'   => 'deepseek',
            'base_url' => 'https://api.deepseek.com/v1',
            'logo'     => '/images/ai/deepseek.svg',
            'models'   => [
                'text' => 'deepseek-chat',
            ],
        ],
        'qianwen' => [
            'name'     => 'Qianwen (通义千问)',
            'driver'   => 'deepseek',
            'base_url' => 'https://dashscope.aliyuncs.com/compatible-mode/v1',
            'logo'     => '/images/ai/qianwen.svg',
            'models'   => [
                'text' => 'qwen-turbo',
            ],
        ],
        'glm' => [
            'name'     => 'GLM (智谱)',
            'driver'   => 'deepseek',
            'base_url' => 'https://open.bigmodel.cn/api/paas/v4',
            'logo'     => '/images/ai/glm.svg',
            'models'   => [
                'text' => 'glm-4',
            ],
        ],
        'kimi' => [
            'name'     => 'Kimi (Moonshot)',
            'driver'   => 'deepseek',
            'base_url' => 'https://api.moonshot.cn/v1',
            'logo'     => '/images/ai/kimi.svg',
            'models'   => [
                'text' => 'moonshot-v1-8k',
            ],
        ],
        'doubao' => [
            'name'     => 'Doubao (豆包)',
            'driver'   => 'deepseek',
            'base_url' => 'https://ark.cn-beijing.volces.com/api/v3',
            'logo'     => '/images/ai/doubao.svg',
            'models'   => [
                'text' => 'doubao-lite-4k',
            ],
        ],
        'hunyuan' => [
            'name'     => 'Hunyuan (混元)',
            'driver'   => 'deepseek',
            'base_url' => 'https://api.hunyuan.cloud.tencent.com/v1',
            'logo'     => '/images/ai/hunyuan.svg',
            'models'   => [
                'text' => 'hunyuan-standard',
            ],
        ],
        'minimax' => [
            'name'     => 'MiniMax',
            'driver'   => 'deepseek',
            'base_url' => 'https://api.minimax.chat/v1',
            'logo'     => '/images/ai/minimax.svg',
            'models'   => [
                'text' => 'MiniMax-Text-01',
            ],
        ],
    ];

    /**
     * Get all providers: from settings + plugins via hook.
     * Falls back to legacy per-provider settings if ai_providers is empty.
     */
    public function getProviders(): array
    {
        $providers = system_setting('ai_providers', []);

        if (is_string($providers)) {
            $providers = json_decode($providers, true) ?: [];
        }

        // Fallback: migrate from legacy {name}_api_key settings
        if (empty($providers)) {
            $providers = $this->migrateFromLegacySettings();
        }

        // Let plugins inject their providers
        return fire_hook_filter('ai.providers', $providers);
    }

    /**
     * Get only user-configured providers (from DB settings, no plugin hooks).
     */
    public function getUserProviders(): array
    {
        $providers = system_setting('ai_providers', []);

        if (is_string($providers)) {
            $providers = json_decode($providers, true) ?: [];
        }

        if (empty($providers)) {
            $providers = $this->migrateFromLegacySettings();
        }

        return $providers;
    }

    /**
     * Get only plugin-injected providers (by running hook and diffing).
     */
    public function getPluginProviders(): array
    {
        $userProviders     = $this->getUserProviders();
        $allProviders      = $this->getProviders();
        $userProviderCodes = array_column($userProviders, 'code');

        return array_values(array_filter($allProviders, function ($p) use ($userProviderCodes) {
            return ! in_array($p['code'] ?? '', $userProviderCodes);
        }));
    }

    /**
     * Build Laravel AI SDK config from provider list.
     * Writes to config('ai.providers.*'), config('ai.default'), config('ai.default_for_images').
     */
    public function buildLaravelAiConfig(): void
    {
        $providers = $this->getProviders();

        foreach ($providers as $provider) {
            $code   = $provider['code'] ?? '';
            $apiKey = $provider['api_key'] ?? '';

            if (empty($code) || empty($apiKey)) {
                continue;
            }

            $driver = $this->normalizeDriver(
                $provider['driver'] ?? 'openai',
                $provider['base_url'] ?? '',
                $code,
            );

            $baseUrl    = $provider['base_url'] ?? '';
            $textModel  = $provider['models']['text'] ?? '';
            $imageModel = $provider['models']['image'] ?? '';

            $config = [
                'driver' => $driver,
                'key'    => $apiKey,
                'model'  => $textModel,
                'models' => array_filter([
                    'text'  => $textModel ? ['default' => $textModel] : null,
                    'image' => $imageModel ? ['default' => $imageModel] : null,
                ]),
            ];

            if ($baseUrl) {
                $config['url'] = $baseUrl;
            }

            config(["ai.providers.{$code}" => $config]);
        }

        // Set default text provider
        $textProvider = system_setting('ai_text_provider')
            ?: system_setting('ai_model')
            ?: 'glm';

        if (config("ai.providers.{$textProvider}")) {
            config(['ai.default' => $textProvider]);
        }

        // Set default image provider
        $imageProvider = system_setting('ai_image_provider');
        $imageModel    = system_setting('ai_image_model');

        if ($imageProvider && config("ai.providers.{$imageProvider}")) {
            config(['ai.default_for_images' => $imageProvider]);
            if ($imageModel) {
                config(["ai.providers.{$imageProvider}.models.image.default" => $imageModel]);
                config(["ai.providers.{$imageProvider}.model" => $imageModel]);
            }
        }
    }

    /**
     * The laravel/ai `openai` driver targets the new `/responses` endpoint
     * which only OpenAI itself serves. Every other "OpenAI-compatible"
     * provider implements `/chat/completions` only — that path lives in the
     * SDK's `deepseek` driver. Rewrite driver='openai' → 'deepseek' for any
     * non-OpenAI host so saved settings from before this fix still work.
     */
    private function normalizeDriver(string $driver, string $baseUrl, string $code): string
    {
        if ($driver !== 'openai') {
            return $driver;
        }

        if ($code === 'openai' || str_contains($baseUrl, '://api.openai.com')) {
            return 'openai';
        }

        return 'deepseek';
    }

    /**
     * Get providers that have an API key configured (for UI select dropdowns).
     *
     * @return array [{code, name}]
     */
    public function getConfiguredProviders(): array
    {
        $providers = $this->getProviders();
        $result    = [];

        foreach ($providers as $provider) {
            if (! empty($provider['api_key'])) {
                $result[] = [
                    'code' => $provider['code'],
                    'name' => $provider['name'] ?? ucfirst($provider['code']),
                ];
            }
        }

        return $result;
    }

    /**
     * Get preset definitions for the "Add Provider" UI.
     */
    public function getPresets(): array
    {
        $presets = [];
        foreach (static::$presets as $code => $preset) {
            $presets[] = array_merge(['code' => $code], $preset);
        }

        return $presets;
    }

    /**
     * Migrate from legacy per-provider settings ({name}_api_key) to unified format.
     */
    private function migrateFromLegacySettings(): array
    {
        $providers = [];

        foreach (static::$presets as $code => $preset) {
            $apiKey = system_setting("{$code}_api_key");
            if (empty($apiKey)) {
                continue;
            }

            $baseUrl = system_setting("{$code}_base_url")
                ?: system_setting("{$code}_proxy_url")
                ?: $preset['base_url'];

            $providers[] = [
                'code'     => $code,
                'name'     => $preset['name'],
                'driver'   => $preset['driver'],
                'api_key'  => $apiKey,
                'base_url' => $baseUrl,
                'enabled'  => (bool) system_setting("{$code}_enabled", false),
                'models'   => $preset['models'] ?? [],
            ];
        }

        return $providers;
    }
}
