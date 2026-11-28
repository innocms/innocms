<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Devtools\Services;

use Illuminate\Contracts\Translation\Loader as LoaderInterface;
use Symfony\Component\Finder\Finder;

class TranslationCheckService
{
    /**
     * Translation groups already loaded from the loader, keyed by "namespace|group|locale".
     *
     * @var array
     */
    private array $loaded = [];

    public function __construct(private LoaderInterface $loader, private string $langPath, private string $fallbackLocale, private string $basePath = '') {}

    /**
     * Check the translation keys used by the Blade views of the given directories.
     *
     * A key is "raw" when the fallback locale lacks it too: every locale then prints the key
     * itself. A key that only some locales lack falls back to the fallback locale and is a
     * lesser problem, so it is reported separately.
     *
     * @param  array  $viewDirs  absolute paths
     * @param  bool  $withHardcoded  also report hardcoded Chinese text
     * @return array{files: int, keys: int, locales: array, raw: array, fallback: array, hardcoded: array}
     */
    public function check(array $viewDirs, bool $withHardcoded = false): array
    {
        $usages  = $this->collectKeyUsages($viewDirs);
        $locales = $this->locales();
        $raw     = [];
        $gap     = [];

        foreach ($usages as $key => $where) {
            $missing = array_filter($locales, fn ($locale) => ! $this->has($key, $locale));
            if (! $missing) {
                continue;
            }

            if (in_array($this->fallbackLocale, $missing, true)) {
                $raw[$key] = $where;
            } else {
                $gap[$key] = array_values($missing);
            }
        }

        return [
            'files'     => iterator_count($this->bladeFiles($viewDirs)),
            'keys'      => count($usages),
            'locales'   => $locales,
            'raw'       => $raw,
            'fallback'  => $gap,
            'hardcoded' => $withHardcoded ? $this->hardcodedCjk($viewDirs) : [],
        ];
    }

    /**
     * Translation keys referenced by the views, with the places they are used.
     *
     * @param  array  $viewDirs  absolute paths
     * @return array key => ["path/relative/to/base.blade.php:12", ...]
     */
    public function collectKeyUsages(array $viewDirs): array
    {
        $usages = [];

        foreach ($this->bladeFiles($viewDirs) as $file) {
            $relative = $this->relativePath($file->getPathname());

            foreach ($this->extractKeys($file->getContents()) as $line => $keys) {
                foreach ($keys as $key) {
                    $usages[$key][] = $relative.':'.$line;
                }
            }
        }

        return $usages;
    }

    /**
     * Does the given locale have the key in its own language files? No fallback is applied.
     *
     * @param  string  $key
     * @param  string  $locale
     * @return bool
     */
    public function has(string $key, string $locale): bool
    {
        [$namespace, $group, $item] = $this->splitKey($key);

        if ($item === '') {
            // A key without group/item ("Showing") only resolves through the JSON translations.
            return $this->usable($this->json($locale)[$key] ?? null);
        }

        $value = $this->lines($namespace, $group, $locale);
        foreach (explode('.', $item) as $segment) {
            if (! is_array($value) || ! array_key_exists($segment, $value)) {
                return false;
            }
            $value = $value[$segment];
        }

        return $this->usable($value);
    }

    /**
     * Lines of the views that print hardcoded Chinese text: comments excluded.
     *
     * @param  array  $viewDirs  absolute paths
     * @return array "path/relative/to/base.blade.php" => [line => text]
     */
    public function hardcodedCjk(array $viewDirs): array
    {
        $found = [];

        foreach ($this->bladeFiles($viewDirs) as $file) {
            $lines     = [];
            $inComment = null;

            foreach (explode("\n", $file->getContents()) as $no => $line) {
                // "sc=Han" is the script itself: plain \p{Han} also matches punctuation shared with Han, like "·".
                $code = $this->stripJsComment($this->stripComment($line, $inComment));

                if (preg_match('/\p{sc=Han}/u', $code)) {
                    $lines[$no + 1] = trim($code);
                }
            }

            if ($lines) {
                $found[$this->relativePath($file->getPathname())] = $lines;
            }
        }

        return $found;
    }

    /**
     * Locales that ship with the application: one directory under lang/.
     *
     * @return array
     */
    public function locales(): array
    {
        $locales = array_map('basename', array_filter(glob($this->langPath.'/*', GLOB_ONLYDIR), fn ($dir) => ! str_starts_with(basename($dir), '.')));

        sort($locales);

        return $locales;
    }

    /**
     * Default directories: the views of every pack under innopacks/.
     *
     * @return array
     */
    public static function defaultViewDirs(string $basePath): array
    {
        return glob($basePath.'/innopacks/*/resources/views', GLOB_ONLYDIR) ?: [];
    }

    /**
     * "ns::group/sub.item.name" -> namespace "ns", group "group/sub", item "item.name".
     * A key without a dot has no group and only exists as a JSON translation.
     *
     * @param  string  $key
     * @return array [?string $namespace, string $group, string $item]
     */
    private function splitKey(string $key): array
    {
        $namespace = null;

        if (str_contains($key, '::')) {
            [$namespace, $key] = explode('::', $key, 2);
        }

        $slash = strrpos($key, '/');
        $dot   = strpos($key, '.', $slash === false ? 0 : $slash + 1);

        return $dot === false
            ? [$namespace, $key, '']
            : [$namespace, substr($key, 0, $dot), substr($key, $dot + 1)];
    }

    /**
     * Keys passed to __(), trans() or @lang() per line. Keys built at runtime ("a.'.$x")
     * cannot be checked and are skipped.
     *
     * @param  string  $source
     * @return array line => keys
     */
    private function extractKeys(string $source): array
    {
        $found     = [];
        $inComment = null;

        foreach (explode("\n", $source) as $no => $line) {
            $line = $this->stripComment($line, $inComment);
            preg_match_all('/(?:__|trans|@lang)\(\s*([\x27"])([a-zA-Z0-9_\/:.\-]+)\1/', $line, $matches);

            // Concatenated keys end right before the quote: "install/common.".
            $keys = array_filter($matches[2], fn ($key) => ! preg_match('/[.\/:\-]$/', $key));

            if ($keys) {
                $found[$no + 1] = array_values($keys);
            }
        }

        return $found;
    }

    /**
     * Drop Blade ({{-- --}}) and HTML (<!-- -->) comments, tracking comments that span several lines.
     *
     * @param  string  $line
     * @param  ?string  $inComment  the closer of the comment the previous line ended in
     * @return string
     */
    private function stripComment(string $line, ?string &$inComment): string
    {
        if ($inComment !== null) {
            if (($end = strpos($line, $inComment)) === false) {
                return '';
            }

            $line      = substr($line, $end + 4);
            $inComment = null;
        }

        while (preg_match('/\{\{--|<!--/', $line, $opened, PREG_OFFSET_CAPTURE)) {
            $closer = $opened[0][0] === '{{--' ? '--}}' : '-->';
            $start  = $opened[0][1];
            $end    = strpos($line, $closer, $start + 4);

            if ($end === false) {
                $inComment = $closer;

                return substr($line, 0, $start);
            }

            $line = substr($line, 0, $start).substr($line, $end + 4);
        }

        return $line;
    }

    /**
     * Drop a JS comment: a full-line one, or a trailing "// ..." that is not part of a URL.
     *
     * @param  string  $line
     * @return string
     */
    private function stripJsComment(string $line): string
    {
        if (preg_match('/^\s*(\/\/|\*|\/\*)/', $line)) {
            return '';
        }

        return preg_replace('/(\s|^)\/\/.*$/', '$1', $line) ?? $line;
    }

    /**
     * @param  string  $file
     * @return string
     */
    private function relativePath(string $file): string
    {
        if ($this->basePath !== '' && str_starts_with($file, $this->basePath)) {
            return ltrim(substr($file, strlen($this->basePath)), '/');
        }

        return $file;
    }

    /**
     * Blade views of the directories. Views under vendor/ are overrides of framework views.
     *
     * @param  array  $viewDirs
     * @return Finder
     */
    private function bladeFiles(array $viewDirs): Finder
    {
        $dirs = array_values(array_filter($viewDirs, 'is_dir'));

        return Finder::create()->in($dirs)->files()->name('*.blade.php')->notPath('#(^|/)vendor/#')->sortByName();
    }

    /**
     * A loaded translation group of one locale.
     *
     * @param  ?string  $namespace
     * @param  string  $group
     * @param  string  $locale
     * @return array
     */
    private function lines(?string $namespace, string $group, string $locale): array
    {
        return $this->loaded[$namespace.'|'.$group.'|'.$locale] ??= (array) $this->loader->load($locale, $group, $namespace);
    }

    /**
     * JSON translations of one locale.
     *
     * @param  string  $locale
     * @return array
     */
    private function json(string $locale): array
    {
        return $this->loaded['*|*|'.$locale] ??= (array) $this->loader->load($locale, '*', '*');
    }

    /**
     * A translation that would print something.
     *
     * @param  mixed  $value
     * @return bool
     */
    private function usable(mixed $value): bool
    {
        return ! is_array($value) && $value !== null && $value !== '';
    }
}
