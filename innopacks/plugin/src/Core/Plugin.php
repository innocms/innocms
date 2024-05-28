<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Plugin\Core;

use Exception;
use Illuminate\Support\Facades\Validator;
use InnoShop\Plugin\Repositories\PluginRepo;
use InnoShop\Plugin\Repositories\SettingRepo;

final class Plugin
{
    public const TYPES = [
        'payment',
        'shipping',
        'theme',
        'feature',
        'fee',
        'social',
        'language',
    ];

    protected string $type;

    protected string $path;

    protected string $code;

    protected string $icon;

    protected array $author = [];

    protected array|string $name;

    protected array|string $description;

    protected array $packageInfo;

    protected string $dirName;

    protected bool $installed;

    protected bool $enabled;

    protected int $priority;

    protected string $version;

    protected array $columns = [];

    public function __construct(string $path, array $packageInfo)
    {
        $this->path        = $path;
        $this->packageInfo = $packageInfo;
        $this->validateConfig();
    }

    /**
     * Set plugin Type
     *
     * @throws Exception
     */
    public function setType(string $type): self
    {
        if (! in_array($type, self::TYPES)) {
            throw new Exception('Invalid plugin type, must be one of '.implode(',', self::TYPES));
        }
        $this->type = $type;

        return $this;
    }

    /**
     * @param  string  $dirName
     * @return $this
     */
    public function setDirname(string $dirName): self
    {
        $this->dirName = $dirName;

        return $this;
    }

    /**
     * @param  string  $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param  string|array  $name
     * @return $this
     */
    public function setName(string|array $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param  string|array  $description
     * @return $this
     */
    public function setDescription(string|array $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param  string  $icon
     * @return $this
     */
    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param  array  $author
     * @return $this
     */
    public function setAuthor(array $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @param  bool  $installed
     * @return $this
     */
    public function setInstalled(bool $installed): self
    {
        $this->installed = $installed;

        return $this;
    }

    /**
     * @param  bool  $enabled
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @param  int  $priority
     * @return $this
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @param  string  $version
     * @return $this
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Set plugin columns.
     *
     * @return $this
     */
    public function setColumns(): self
    {
        $columnsPath = $this->path.DIRECTORY_SEPARATOR.'columns.php';
        if (! file_exists($columnsPath)) {
            return $this;
        }
        $this->columns = require_once $columnsPath;

        return $this;
    }

    /**
     * Get name from config
     *
     * @return array|string
     */
    public function getName(): array|string
    {
        return $this->name;
    }

    /**
     * Get current locale name
     *
     * @return string
     */
    public function getLocaleName(): string
    {
        $currentLocale = panel_locale();

        if (is_array($this->name)) {
            if ($this->name[$currentLocale] ?? '') {
                return $this->name[$currentLocale];
            }

            return array_values($this->name)[0];
        }

        return (string) $this->name;
    }

    /**
     * Get description from config
     *
     * @return array|string
     */
    public function getDescription(): array|string
    {
        return $this->description;
    }

    /**
     * Get current local description
     *
     * @return string
     */
    public function getLocaleDescription(): string
    {
        $currentLocale = panel_locale();

        if (is_array($this->description)) {
            if ($this->description[$currentLocale] ?? '') {
                return $this->description[$currentLocale];
            }

            return array_values($this->description)[0];
        }

        return (string) $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDirname(): string
    {
        return $this->dirName;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getAuthor(): array
    {
        return $this->author;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getEditUrl(): string
    {
        return panel_route('plugins.edit', ['plugin' => $this->code]);
    }

    public function checkActive(): bool
    {
        return PluginRepo::getInstance()->checkActive($this->code);
    }

    public function checkInstalled(): bool
    {
        return PluginRepo::getInstance()->installed($this->code);
    }

    public function checkPriority(): int
    {
        return PluginRepo::getInstance()->getPriority($this->code);
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Retrieve the corresponding setting fields of the plugin,
     * and obtain the field values that have been stored in the database.
     *
     * @return array
     */
    public function getColumns(): array
    {
        $this->columns[] = SettingRepo::getInstance()->getPluginActiveColumn();
        $existValues     = SettingRepo::getInstance()->getPluginColumns($this->code);
        foreach ($this->columns as $index => $column) {
            $dbColumn = $existValues[$column['name']] ?? null;
            $value    = $dbColumn ? $dbColumn->value : null;
            if ($column['name'] == 'active') {
                $value = (int) $value;
            }
            $this->columns[$index]['value'] = $value;
        }

        return $this->columns;
    }

    /**
     * Handle the multilingual settings of plugin backend fields with the priority: label > label_key.
     * If there is a label field, return it directly; if there is no label field, then use label_key for translation.
     */
    public function handleLabel(): void
    {
        $this->columns = collect($this->columns)->map(function ($item) {
            $item = $this->transLabel($item);
            if (isset($item['options'])) {
                $item['options'] = collect($item['options'])->map(function ($option) {
                    return $this->transLabel($option);
                })->toArray();
            }

            return $item;
        })->toArray();
    }

    /**
     * Get plugin boot class file path.
     *
     * @return string
     */
    public function getBootFile(): string
    {
        return $this->getPath().'/Boot.php';
    }

    /**
     * Column validation
     */
    public function validateConfig(): void
    {
        Validator::validate($this->packageInfo, [
            'type'        => 'required',
            'name'        => 'required',
            'description' => 'required',
            'code'        => 'required|string|min:3|max:64',
            'version'     => 'required|string',
        ]);
    }

    /**
     * Column validation
     *
     * @param  $requestData
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateColumns($requestData): \Illuminate\Contracts\Validation\Validator
    {
        $rules = array_column($this->columns, 'rules', 'name');

        return Validator::make($requestData, $rules);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge([
            'name'    => $this->name,
            'version' => $this->getVersion(),
            'path'    => $this->path,
        ], $this->packageInfo);
    }

    /**
     * Translate label
     * @param  $item
     * @return mixed
     */
    private function transLabel($item): mixed
    {
        $labelKey = $item['label_key'] ?? '';
        $label    = $item['label']     ?? '';
        if (empty($label) && $labelKey) {
            $languageKey   = "$this->dirName::$labelKey";
            $item['label'] = trans($languageKey);
        }

        $descriptionKey = $item['description_key'] ?? '';
        $description    = $item['description']     ?? '';
        if (empty($description) && $descriptionKey) {
            $languageKey         = "$this->dirName::$descriptionKey";
            $item['description'] = trans($languageKey);
        }

        return $item;
    }
}
