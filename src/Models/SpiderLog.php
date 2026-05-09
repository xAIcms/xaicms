<?php
// php_backend/src/Models/SpiderLog.php

require_once __DIR__ . '/../Config/Database.php';

class SpiderLog {
    public static function log($botName, $botType, $ip, $userAgent, $path, $statusCode = 200) {
        $pdo = Database::getInstance()->getConnection();
        $sql = "INSERT INTO spider_logs (bot_name, bot_type, ip_address, user_agent, path, status_code) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$botName, $botType, $ip, $userAgent, $path, $statusCode]);
    }

    /**
     * Get real client IP, handling Cloudflare and Proxies
     */
    public static function getRealIp() {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            return $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ips = explode(',', $_SERVER["HTTP_X_FORWARDED_FOR"]);
            return trim($ips[0]);
        }
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        }
        return $_SERVER["REMOTE_ADDR"] ?? '0.0.0.0';
    }

    /**
     * Identify bot from User Agent
     * @param string $userAgent
     * @return array|null [name, type] or null if not found
     */
    public static function identifyBot($userAgent) {
        if (empty($userAgent)) return null;

        $userAgent = strtolower($userAgent);

        $bots = [
            // Search Engines
            'googlebot' => ['name' => 'Googlebot', 'type' => 'SearchEngine'],
            'bingbot' => ['name' => 'Bingbot', 'type' => 'SearchEngine'],
            'baiduspider' => ['name' => 'Baiduspider', 'type' => 'SearchEngine'],
            'sogou' => ['name' => 'Sogou Spider', 'type' => 'SearchEngine'],
            'yandex' => ['name' => 'YandexBot', 'type' => 'SearchEngine'],
            '360spider' => ['name' => '360Spider', 'type' => 'SearchEngine'],
            'haosou' => ['name' => '360Spider', 'type' => 'SearchEngine'],
            'sosospider' => ['name' => 'Soso Spider', 'type' => 'SearchEngine'],
            'youdaobot' => ['name' => 'Youdao Bot', 'type' => 'SearchEngine'],
            'duckduckbot' => ['name' => 'DuckDuckBot', 'type' => 'SearchEngine'],
            'slurp' => ['name' => 'Yahoo Slurp', 'type' => 'SearchEngine'],
            'msnbot' => ['name' => 'MSNBot', 'type' => 'SearchEngine'],
            
            // AI Crawlers & LLMs
            'gptbot' => ['name' => 'GPTBot', 'type' => 'AIModel'],
            'chatgpt' => ['name' => 'ChatGPT-User', 'type' => 'AIModel'],
            'bytespider' => ['name' => 'Bytespider', 'type' => 'AIModel'], // ByteDance/Douyin
            'bytedance' => ['name' => 'ByteDance', 'type' => 'AIModel'],
            'claudebot' => ['name' => 'ClaudeBot', 'type' => 'AIModel'],
            'anthropic' => ['name' => 'Anthropic-AI', 'type' => 'AIModel'],
            'ccbot' => ['name' => 'CCBot', 'type' => 'AIModel'], // CommonCrawl (used by many LLMs)
            'cohere' => ['name' => 'Cohere-AI', 'type' => 'AIModel'],
            'google-extended' => ['name' => 'Google-Extended', 'type' => 'AIModel'], // Google Bard/Gemini
            'diffbot' => ['name' => 'Diffbot', 'type' => 'AIModel'],
            'facebookbot' => ['name' => 'FacebookBot', 'type' => 'AIModel'], // LLaMA training?
            'omgilibot' => ['name' => 'OmgiliBot', 'type' => 'AIModel'],
            'anthropic-ai' => ['name' => 'Anthropic-AI', 'type' => 'AIModel'],
            
            // Social Media
            'facebookexternalhit' => ['name' => 'Facebook', 'type' => 'SocialMedia'],
            'twitterbot' => ['name' => 'Twitter', 'type' => 'SocialMedia'],
            'linkedinbot' => ['name' => 'LinkedIn', 'type' => 'SocialMedia'],
            'pinterest' => ['name' => 'Pinterest', 'type' => 'SocialMedia'],
            'whatsapp' => ['name' => 'WhatsApp', 'type' => 'SocialMedia'],
            'telegrambot' => ['name' => 'Telegram', 'type' => 'SocialMedia'],
            'discordbot' => ['name' => 'Discord', 'type' => 'SocialMedia'],
            
            // Others
            'applebot' => ['name' => 'Applebot', 'type' => 'SearchEngine'], // Often used for Spotlight/Siri
            'petalbot' => ['name' => 'PetalBot', 'type' => 'SearchEngine'], // Huawei
            'semrushbot' => ['name' => 'SemrushBot', 'type' => 'SEOTool'],
            'ahrefsbot' => ['name' => 'AhrefsBot', 'type' => 'SEOTool'],
            'mj12bot' => ['name' => 'MJ12bot', 'type' => 'SEOTool'],
            'dotbot' => ['name' => 'DotBot', 'type' => 'SEOTool'],
            'rogerbot' => ['name' => 'Rogerbot', 'type' => 'SEOTool'], // Moz
            'screaming frog' => ['name' => 'Screaming Frog', 'type' => 'SEOTool'],
        ];

        foreach ($bots as $key => $info) {
            if (strpos($userAgent, $key) !== false) {
                return $info;
            }
        }
        return null;
    }

    public static function getRecent($limit = 50) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM spider_logs ORDER BY visited_at DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getAll($limit = 50, $offset = 0) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM spider_logs ORDER BY visited_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countAll() {
        $pdo = Database::getInstance()->getConnection();
        return $pdo->query("SELECT COUNT(*) FROM spider_logs")->fetchColumn();
    }

    public static function getStats($days = 7) {
        $pdo = Database::getInstance()->getConnection();
        
        // Total visits in last N days
        $sql = "SELECT COUNT(*) FROM spider_logs WHERE visited_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$days]);
        $total = $stmt->fetchColumn();

        // Visits by bot name
        $sql = "SELECT bot_name, COUNT(*) as count FROM spider_logs WHERE visited_at >= DATE_SUB(NOW(), INTERVAL ? DAY) GROUP BY bot_name ORDER BY count DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$days]);
        $byBot = $stmt->fetchAll();

        return [
            'total' => $total,
            'by_bot' => $byBot
        ];
    }
}
