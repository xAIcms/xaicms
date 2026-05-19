<?php
// src/Core/Template.php
// Template manager — scan, activate, metadata

require_once __DIR__ . '/../Models/Settings.php';

class Template
{
    /**
     * Scan templates directory and return all found templates
     */
    public static function scan(): array
    {
        $templates = [];
        $dir = __DIR__ . '/../../templates';

        if (!is_dir($dir)) return $templates;

        foreach (scandir($dir) as $name) {
            if ($name === '.' || $name === '..') continue;
            if ($name === 'admin' || $name === 'partials' || $name === 'auth' || $name === 'user') continue;
            
            $templateDir = "$dir/$name";
            if (!is_dir($templateDir)) continue;

            $meta = self::getMeta($templateDir);
            $meta['slug'] = $name;
            $meta['active'] = (self::getCurrent() === $name);
            $templates[$name] = $meta;
        }

        // Always include default
        if (!isset($templates['default'])) {
            $templates['default'] = [
                'name' => 'Default',
                'slug' => 'default',
                'description' => 'Built-in default template',
                'version' => '1.0.0',
                'author' => 'xAIcms',
                'active' => true,
                'thumbnail' => '',
            ];
        }

        return $templates;
    }

    /**
     * Get currently active template slug
     */
    public static function getCurrent(): string
    {
        try {
            return Settings::get('active_template', 'default');
        } catch (\Exception $e) {
            return 'default';
        }
    }

    /**
     * Activate a template
     */
    public static function activate(string $slug): bool
    {
        $templateDir = __DIR__ . "/../../templates/$slug";
        if (!is_dir($templateDir)) return false;

        Settings::set('active_template', $slug);
        return true;
    }

    /**
     * Parse template metadata from template.json or template.php doc comment
     */
    public static function getMeta(string $templateDir): array
    {
        $meta = [
            'name' => basename($templateDir),
            'description' => '',
            'version' => '1.0.0',
            'author' => 'Unknown',
            'thumbnail' => '',
        ];

        // Try template.json first (preferred)
        $jsonFile = "$templateDir/template.json";
        if (file_exists($jsonFile)) {
            $jsonMeta = json_decode(file_get_contents($jsonFile), true);
            if ($jsonMeta) {
                return array_merge($meta, $jsonMeta);
            }
        }

        // Fallback: parse template.php doc comment
        $phpFile = "$templateDir/index.php";
        if (!file_exists($phpFile)) {
            $phpFile = "$templateDir/home.php";
        }
        if (file_exists($phpFile)) {
            $content = file_get_contents($phpFile);
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
        }

        // Check for screenshot/thumbnail
        foreach (['screenshot.png', 'screenshot.jpg', 'thumbnail.png', 'thumbnail.jpg'] as $img) {
            if (file_exists("$templateDir/$img")) {
                $meta['thumbnail'] = "/templates/" . basename($templateDir) . "/$img";
                break;
            }
        }

        return $meta;
    }

    /**
     * Get template path for a file
     */
    public static function getPath(string $file): string
    {
        $current = self::getCurrent();
        $path = TEMPLATES_PATH . "/$current/$file";

        // Fallback to default if file doesn't exist in current template
        if (!file_exists($path) && $current !== 'default') {
            $path = TEMPLATES_PATH . "/default/$file";
        }

        return $path;
    }
}

// Define TEMPLATES_PATH constant if not already defined
if (!defined('TEMPLATES_PATH')) {
    define('TEMPLATES_PATH', __DIR__ . '/../../templates');
}
