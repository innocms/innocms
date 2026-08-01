<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Translation;

use Illuminate\Translation\FileLoader;

/**
 * FileLoader extension that allows multiple paths per namespace.
 *
 * Standard FileLoader overwrites hints[namespace] on every addNamespace() call,
 * so two paths under one namespace cannot coexist. This subclass:
 *   - accepts single-path namespaces (preserves Laravel compat with reflection copy)
 *   - upgrades them to multi-path arrays on subsequent addNamespace() calls
 *   - merges translations from each path (later registrations win on conflicts)
 *
 * Use case: lang/front/* (universal UI labels) + innopacks/front/lang/* (default
 * theme copy) both under the `front::` namespace.
 */
class MultiPathFileLoader extends FileLoader
{
    /**
     * Stack hint paths per namespace. Single-string values (copied from a
     * legacy FileLoader via reflection) are normalised to arrays on first use.
     *
     * @param  string  $namespace
     * @param  string  $hint
     * @return void
     */
    public function addNamespace($namespace, $hint): void
    {
        if ($namespace === '*') {
            parent::addNamespace($namespace, $hint);

            return;
        }

        // Normalise any pre-existing single-string hint for this namespace.
        if (isset($this->hints[$namespace]) && ! is_array($this->hints[$namespace])) {
            $this->hints[$namespace] = [$this->hints[$namespace]];
        }

        $this->hints[$namespace][] = $hint;
    }

    /**
     * Merge every registered hint path for the namespace. Later registrations
     * override earlier ones for the same key, mirroring Laravel's vendor
     * override semantics.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string  $namespace
     * @return array
     */
    protected function loadNamespaced($locale, $group, $namespace): array
    {
        if (! isset($this->hints[$namespace]) || empty($this->hints[$namespace])) {
            return [];
        }

        $lines = [];
        foreach ((array) $this->hints[$namespace] as $hint) {
            $path = "{$hint}/{$locale}/{$group}.php";
            if ($this->files->exists($path)) {
                $loaded = $this->files->getRequire($path);
                if (is_array($loaded)) {
                    $lines = array_merge($lines, $loaded);
                }
            }
        }

        return $this->loadNamespaceOverrides($lines, $locale, $group, $namespace);
    }
}
