<?php
// src/Core/Plugin.php
// Plugin loader — scans plugins/ directory, loads active plugins, manages activation

require_once __DIR__ . '/../Models/Settings.php';

class Plugin
{
    private static $loaded = [];

    /**
     * Scan plugins directory and return all found plugins with their metadata
     */
    public static function scan(): array
    {
        $plugins = [];
        $dir = __DIR__ . '/../../plugins';

        if (!is_dir($dir)) return $plugins;

        foreach (scandir($dir) as $name) {
            if ($name === '.' || $name === '..') continue;
            $pluginFile = "$dir/$name/plugin.php";
            if (!file_exists($pluginFile)) continue;

            $meta = self::getMeta($pluginFile);
            $meta['slug'] = $name;
            $meta['active'] = in_array($name, self::getActive());
            $plugins[$name] = $meta;
        }

        return $plugins;
    }

    /**
     * Load all active plugins
     */
    public static function loadActive(): void
    {
        $active = self::getActive();
        $dir = __DIR__ . '/../../plugins';

        foreach ($active as $slug) {
            $pluginFile = "$dir/$slug/plugin.php";
            if (file_exists($pluginFile)) {
                require_once $pluginFile;
                self::$loaded[] = $slug;
            }
        }
    }

    /**
     * Get active plugin slugs from database
     */
    public static function getActive(): array
    {
        try {
            $raw = Settings::get('active_plugins');
            return $raw ? json_decode($raw, true) : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Activate a plugin
     */
    public static function activate(string $slug): bool
    {
        $pluginFile = __DIR__ . "/../../plugins/$slug/plugin.php";
        if (!file_exists($pluginFile)) return false;

        $active = self::getActive();
        if (!in_array($slug, $active)) {
            $active[] = $slug;
            Settings::set('active_plugins', json_encode($active));
        }
        return true;
    }

    /**
     * Deactivate a plugin
     */
    public static function deactivate(string $slug): bool
    {
        $active = self::getActive();
        $active = array_filter($active, function($s) use ($slug) { return $s !== $slug; });
        Settings::set('active_plugins', json_encode(array_values($active)));
        return true;
    }

    /**
     * Parse plugin metadata from doc comment
     * Format:
     *   /*
     *    * Plugin Name: My Plugin
     *    * Description: Does something
     *    * Version: 1.0.0
     *    * Author: xAI
     *    *\/
     */
    public static function getMeta(string $pluginFile): array
    {
        $meta = [
            'name' => basename(dirname($pluginFile)),
            'description' => '',
            'version' => '0.1.0',
            'author' => 'Unknown',
        ];

        $content = file_get_contents($pluginFile);
        if (preg_match('/\/\*\*(.*?)\*\//s', $content, $matches)) {
            $doc = $matches[1];
            $lines = explode("\n", $doc);
            foreach ($lines as $line) {
                $line = trim($line, " *\t\r\n");
                if (preg_match('/^(\w[\w\s]+):\s*(.+)/', $line, $m)) {
                    $key = strtolower(str_replace(' ', '_', $m[1]));
                    $meta[$key] = trim($m[2]);
                }
            }
        }

        return $meta;
    }

    /**
     * Get list of currently loaded plugin slugs
     */
    public static function loaded(): array
    {
        return self::$loaded;
    }
}
