<?php
// src/Core/Plan.php — Plan/Feature gate helper

class Plan {
    private static $plan = null;
    private static $features = null;

    public static function current(): string {
        if (self::$plan === null) {
            $settings = Settings::getAll();
            self::$plan = $settings['plan'] ?? 'free';
        }
        return self::$plan;
    }

    public static function features(): array {
        if (self::$features === null) {
            $settings = Settings::getAll();
            $json = $settings['plan_features'] ?? '{}';
            self::$features = json_decode($json, true) ?: [];
        }
        return self::$features;
    }

    public static function has(string $feature): bool {
        $plan = self::current();
        $features = self::features();
        return !empty($features[$plan][$feature]);
    }

    public static function limit(string $feature): int {
        $plan = self::current();
        $features = self::features();
        return (int)($features[$plan][$feature] ?? 0);
    }

    public static function upgrade(string $newPlan): bool {
        $features = self::features();
        if (!isset($features[$newPlan])) return false;
        Settings::update('plan', $newPlan);
        self::$plan = $newPlan;
        return true;
    }

    public static function planName(): string {
        $features = self::features();
        $plan = self::current();
        return $features[$plan]['name'] ?? ucfirst($plan);
    }
}
