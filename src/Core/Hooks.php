<?php
// src/Core/Hooks.php
// WordPress-style action/filter hook system

class Hooks
{
    private static $actions = [];
    private static $filters = [];
    private static $currentFilter = [];

    /**
     * Register an action callback
     * add_action('article_saved', function($article) { ... }, 10, 1);
     */
    public static function addAction(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        self::$actions[$tag][$priority][] = [
            'callback' => $callback,
            'args' => $acceptedArgs,
        ];
    }

    /**
     * Register a filter callback
     * $title = apply_filters('article_title', $title, $article);
     */
    public static function addFilter(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        self::$filters[$tag][$priority][] = [
            'callback' => $callback,
            'args' => $acceptedArgs,
        ];
    }

    /**
     * Execute all callbacks for an action
     */
    public static function doAction(string $tag, ...$args): void
    {
        if (!isset(self::$actions[$tag])) return;

        $sorted = self::$actions[$tag];
        ksort($sorted);

        foreach ($sorted as $priority => $callbacks) {
            foreach ($callbacks as $cb) {
                $numArgs = min($cb['args'], count($args));
                call_user_func_array($cb['callback'], array_slice($args, 0, $numArgs));
            }
        }
    }

    /**
     * Apply filters to a value
     * $value = apply_filters('tag', $value, ...$context);
     */
    public static function applyFilters(string $tag, $value, ...$args)
    {
        if (!isset(self::$filters[$tag])) return $value;

        array_unshift(self::$currentFilter, $tag);

        $sorted = self::$filters[$tag];
        ksort($sorted);

        foreach ($sorted as $priority => $callbacks) {
            foreach ($callbacks as $cb) {
                $cbArgs = array_merge([$value], $args);
                $numArgs = min($cb['args'], count($cbArgs));
                $value = call_user_func_array($cb['callback'], array_slice($cbArgs, 0, $numArgs));
            }
        }

        array_shift(self::$currentFilter);
        return $value;
    }

    /**
     * Get all registered action tags
     */
    public static function getActions(): array
    {
        return array_keys(self::$actions);
    }

    /**
     * Get all registered filter tags
     */
    public static function getFilters(): array
    {
        return array_keys(self::$filters);
    }

    /**
     * Remove all hooks (for testing)
     */
    public static function reset(): void
    {
        self::$actions = [];
        self::$filters = [];
        self::$currentFilter = [];
    }
}
