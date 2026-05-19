<?php
// src/Core/I18n.php — Translation system for xAI CMS

class I18n {
    private static $translations = [];
    private static $locale = 'zh-CN';

    /**
     * Load a language pack.
     */
    public static function load(string $locale): void {
        $file = __DIR__ . '/../../lang/' . $locale . '.php';
        if (file_exists($file)) {
            $pack = require $file;
            if (is_array($pack)) {
                self::$translations = $pack;
                self::$locale = $locale;
            }
        }
    }

    /**
     * Get the current locale code.
     */
    public static function locale(): string {
        return self::$locale;
    }

    /**
     * Translate a string. Returns original if no translation found.
     */
    public static function t(string $key, string $default = ''): string {
        if (isset(self::$translations[$key])) {
            return self::$translations[$key];
        }
        return $default ?: $key;
    }

    /**
     * Translate with sprintf-style placeholders.
     *
     * Example: __('Created %d articles', 5) → '创建了 5 篇文章'
     */
    public static function tf(string $key, ...$args): string {
        $translated = self::t($key);
        if (!empty($args)) {
            $translated = sprintf($translated, ...$args);
        }
        return $translated;
    }

    /**
     * Get all translations (for debugging).
     */
    public static function all(): array {
        return self::$translations;
    }
}
