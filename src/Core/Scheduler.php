<?php
// src/Core/Scheduler.php
// Built-in task scheduler — runs due tasks on page visits (WordPress pseudo-cron pattern)
// No external cron job needed. Tasks run when someone visits the site.

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Models/Settings.php';

class Scheduler
{
    private static $executed = false; // Prevent multiple runs per request

    /**
     * Ensure the schedules table exists
     */
    public static function ensureTable(): void
    {
        $pdo = Database::getInstance()->getConnection();
        $pdo->exec("CREATE TABLE IF NOT EXISTS schedules (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            task_name VARCHAR(64) NOT NULL,
            description VARCHAR(255) DEFAULT '',
            next_run_at DATETIME NOT NULL,
            interval_seconds INT UNSIGNED NOT NULL DEFAULT 86400 COMMENT 'Seconds between runs',
            last_run_at DATETIME DEFAULT NULL,
            config TEXT COMMENT 'JSON config for the task',
            enabled TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uk_task_name (task_name)
        )");
    }

    /**
     * Check and run any due tasks. Call this on every page load.
     * Very lightweight — just a SQL query if no tasks are due.
     */
    public static function tick(): void
    {
        if (self::$executed) return;
        self::$executed = true;

        self::ensureTable();

        $pdo = Database::getInstance()->getConnection();
        $now = date('Y-m-d H:i:s');

        $tasks = $pdo->prepare(
            "SELECT * FROM schedules WHERE enabled = 1 AND next_run_at <= ? ORDER BY next_run_at ASC LIMIT 3"
        );
        $tasks->execute([$now]);

        while ($task = $tasks->fetch()) {
            self::runTask($task);
        }
    }

    /**
     * Execute a single task
     */
    private static function runTask(array $task): void
    {
        $pdo = Database::getInstance()->getConnection();

        // Mark as running (update next_run_at to prevent double execution)
        $next = date('Y-m-d H:i:s', time() + $task['interval_seconds']);
        $pdo->prepare("UPDATE schedules SET last_run_at = ?, next_run_at = ?, updated_at = NOW() WHERE id = ?")
            ->execute([date('Y-m-d H:i:s'), $next, $task['id']]);

        $config = json_decode($task['config'] ?? '{}', true) ?: [];

        try {
            switch ($task['task_name']) {
                case 'publish_article':
                    self::runPublishArticle($config);
                    break;
                case 'generate_article':
                    self::runGenerateArticle($config);
                    break;
                default:
                    // Custom task — fire hook for plugins
                    do_action('scheduler_' . $task['task_name'], $config);
                    break;
            }
        } catch (\Exception $e) {
            error_log("[Scheduler] Task {$task['task_name']} failed: " . $e->getMessage());
        }
    }

    /**
     * Publish a scheduled (draft) article
     */
    private static function runPublishArticle(array $config): void
    {
        $articleId = $config['article_id'] ?? 0;
        if (!$articleId) return;

        require_once __DIR__ . '/../Models/Article.php';
        Article::update($articleId, ['status' => 1, 'published_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Generate an article via AI and publish
     */
    private static function runGenerateArticle(array $config): void
    {
        require_once __DIR__ . '/../Models/Article.php';
        require_once __DIR__ . '/../Models/AiModel.php';
        require_once __DIR__ . '/../Models/Settings.php';

        $modelId = $config['model_id'] ?? 0;
        $categoryId = $config['category_id'] ?? 0;
        $language = $config['language'] ?? 'zh-CN';
        $region = $config['region'] ?? 'CN';
        $topic = $config['topic'] ?? '';
        $keywords = $config['keywords'] ?? '';
        $tone = $config['tone'] ?? 'professional';
        $wordCount = $config['word_count'] ?? 800;
        $authorName = $config['author_name'] ?? 'AI Editor';
        $customPrompt = $config['prompt'] ?? '';

        if (empty($topic)) return;

        // Find active AI model
        $model = $modelId ? AiModel::find($modelId) : null;
        if (!$model) {
            $models = AiModel::getActive();
            if (!empty($models)) $model = $models[0];
        }
        if (!$model) return;

        // Build prompt
        $toneGuide = match ($tone) {
            'casual' => 'Use a friendly, conversational style.',
            'technical' => 'Write with technical depth and precision. Include technical terms and explanations.',
            'journalistic' => 'Write in a news/journalistic style. Be factual and objective.',
            'persuasive' => 'Write persuasively. Make compelling arguments.',
            'educational' => 'Write as a tutorial or educational guide. Be clear and instructional.',
            default => 'Write in a professional tone.',
        };

        $prompt = $customPrompt ?: "Write a {$wordCount}-word article in {$language}.\n\nTopic: {$topic}\nKeywords to include: {$keywords}\nTone: {$tone}\n{$toneGuide}\n\nStructure: Title, introduction, key points with subheadings, conclusion.";

        // Replace placeholders
        $prompt = str_replace(
            ['{topic}', '{keywords}', '{tone}', '{word_count}'],
            [$topic, $keywords, $tone, $wordCount],
            $prompt
        );

        // Call AI
        $content = self::callAI($model, $prompt);
        if (!$content) return;

        // Extract title from first line
        $lines = explode("\n", trim($content));
        $title = trim($lines[0], "# \t\n\r\0\x0B");
        if (mb_strlen($title) > 120) {
            $title = mb_substr($title, 0, 120);
        }
        // If title looks like a heading marker, use topic
        if (empty($title) || preg_match('/^[#\-\*\d\.]+$/', $title)) {
            $title = $topic;
        }

        $slug = \SlugGenerator::generate($title);

        Article::create([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'summary' => mb_substr(strip_tags($content), 0, 200),
            'category_id' => $categoryId,
            'language' => $language,
            'geo_region' => $region,
            'status' => 1,
            'published_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Call AI model API
     */
    private static function callAI(array $model, string $prompt): ?string
    {
        $apiKey = $model['api_key'];
        $baseUrl = rtrim($model['base_url'], '/');
        $modelName = $model['model_name'];

        if (empty($apiKey) || empty($baseUrl)) return null;

        $url = "$baseUrl/chat/completions";

        $payload = json_encode([
            'model' => $modelName,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_TIMEOUT => 60,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) return null;

        $data = json_decode($response, true);
        return $data['choices'][0]['message']['content'] ?? null;
    }

    /**
     * Create a new scheduled task
     */
    public static function create(string $taskName, array $config, int $intervalSeconds = 86400, string $description = ''): int
    {
        self::ensureTable();
        $pdo = Database::getInstance()->getConnection();

        // Calculate next run time
        $now = time();
        $runAtTime = $config['run_at_time'] ?? null;
        $runOnDays = $config['run_on_days'] ?? [];

        if ($runAtTime) {
            // Calculate next run at the specified time
            $todayAt = strtotime(date('Y-m-d') . ' ' . $runAtTime . ':00');
            if ($todayAt <= $now) {
                $todayAt = strtotime('+1 day', strtotime(date('Y-m-d') . ' ' . $runAtTime . ':00'));
            }
            $nextRunTs = $todayAt;

            // For weekly: find next matching day
            if (!empty($runOnDays)) {
                $nextRunTs = null;
                for ($i = 0; $i < 7; $i++) {
                    $checkDay = strtotime("+$i day", strtotime(date('Y-m-d') . ' ' . $runAtTime . ':00'));
                    $dayOfWeek = (int)date('N', $checkDay) - 1; // 0=Mon, 6=Sun
                    if (in_array((string)$dayOfWeek, $runOnDays) || in_array((int)$dayOfWeek, $runOnDays)) {
                        if ($checkDay > $now) {
                            $nextRunTs = $checkDay;
                            break;
                        }
                    }
                }
                if (!$nextRunTs) {
                    $nextRunTs = $todayAt + 604800; // Fallback: next week
                }
            }
        } else {
            $nextRunTs = $now + $intervalSeconds;
        }

        $nextRun = date('Y-m-d H:i:s', $nextRunTs);

        $pdo->prepare(
            "INSERT INTO schedules (task_name, description, next_run_at, interval_seconds, config) VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE interval_seconds = ?, config = ?, enabled = 1, next_run_at = ?, updated_at = NOW()"
        )->execute([
            $taskName, $description, $nextRun, $intervalSeconds, json_encode($config),
            $intervalSeconds, json_encode($config), $nextRun
        ]);

        return (int) $pdo->lastInsertRowId();
    }

    /**
     * Get all scheduled tasks
     */
    public static function getAll(): array
    {
        self::ensureTable();
        $pdo = Database::getInstance()->getConnection();
        return $pdo->query("SELECT * FROM schedules ORDER BY next_run_at ASC")->fetchAll();
    }

    /**
     * Delete a schedule
     */
    public static function delete(int $id): void
    {
        self::ensureTable();
        $pdo = Database::getInstance()->getConnection();
        $pdo->prepare("DELETE FROM schedules WHERE id = ?")->execute([$id]);
    }

    /**
     * Toggle enabled/disabled
     */
    public static function toggle(int $id): void
    {
        self::ensureTable();
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE schedules SET enabled = NOT enabled WHERE id = ?");
        $stmt->execute([$id]);
    }
}
