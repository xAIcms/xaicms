<?php
// php_backend/src/Models/ApiConfig.php

require_once __DIR__ . '/../Config/Database.php';

class ApiConfig {
    public static function getAll() {
        $pdo = Database::getInstance()->getConnection();
        
        // Ensure columns exist (Auto-migration) to prevent missing column errors
        self::ensureColumns();

        // Check tables existence first
        $tableExists = $pdo->query("SHOW TABLES LIKE 'ai_models'")->rowCount() > 0;
        $usersTableExists = $pdo->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
        $schemesTableExists = $pdo->query("SHOW TABLES LIKE 'ai_schemes'")->rowCount() > 0;
        
        // Sync orphans if schemes table exists
        if ($schemesTableExists) {
            self::syncOrphanSchemes($pdo);
        }
        
        $sql = "SELECT ac.*";

        
        if ($tableExists) {
            $sql .= ", am.name as ai_model_name";
        } else {
            $sql .= ", NULL as ai_model_name";
        }
        
        if ($usersTableExists) {
            $sql .= ", u.name as user_name, u.points as user_points";
        } else {
            $sql .= ", NULL as user_name, 0 as user_points";
        }
        
        if ($schemesTableExists) {
            $sql .= ", s.status as scheme_status, s.frozen_points, s.target_count, s.generated_count";
        } else {
            $sql .= ", NULL as scheme_status, 0 as frozen_points, 0 as target_count, 0 as generated_count";
        }
        
        $sql .= " FROM api_configs ac";
        
        if ($tableExists) {
            $sql .= " LEFT JOIN ai_models am ON ac.ai_model_id = am.id";
        }
        
        if ($usersTableExists) {
            $sql .= " LEFT JOIN users u ON ac.user_id = u.id";
        }
        
        if ($schemesTableExists) {
            $sql .= " LEFT JOIN ai_schemes s ON ac.scheme_id = s.id";
        }
        
        $sql .= " ORDER BY ac.created_at DESC";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    public static function find($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM api_configs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findByKey($key) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM api_configs WHERE api_key = ? AND status = 1");
        $stmt->execute([$key]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $pdo = Database::getInstance()->getConnection();
        
        // Ensure columns exist (Auto-migration)
        self::ensureColumns();

        if (empty($data['uuid'])) {
            $data['uuid'] = self::generateUuid();
        }
        
        // Generate API Key if not present (usually auto-generated)
        $apiKey = self::generateApiKey();
        
        $sql = "INSERT INTO api_configs (
            uuid, name, api_key, geo_region, language, category_id, 
            keywords, keyword_count, promotion_info, status,
            article_status, insert_image_count, insert_image_position,
            image_source_type, custom_image_urls, media_category_id, auto_link, ai_model_id,
            user_id, scheme_id, daily_limit
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['uuid'],
            $data['name'],
            $apiKey,
            $data['geo_region'] ?? 'CN',
            $data['language'] ?? 'zh-CN',
            !empty($data['category_id']) ? (int)$data['category_id'] : 0,
            $data['keywords'] ?? '',
            !empty($data['keyword_count']) ? (int)$data['keyword_count'] : 1,
            $data['promotion_info'] ?? '',
            isset($data['status']) && $data['status'] !== '' ? (int)$data['status'] : 1,
            isset($data['article_status']) && $data['article_status'] !== '' ? (int)$data['article_status'] : 1,
            !empty($data['insert_image_count']) ? (int)$data['insert_image_count'] : 0,
            $data['insert_image_position'] ?? 'random',
            $data['image_source_type'] ?? 'picsum',
            $data['custom_image_urls'] ?? '',
            !empty($data['media_category_id']) ? (int)$data['media_category_id'] : 0,
            !empty($data['auto_link']) ? (int)$data['auto_link'] : 0,
            !empty($data['ai_model_id']) ? (int)$data['ai_model_id'] : 0,
            !empty($data['user_id']) ? (int)$data['user_id'] : 0,
            !empty($data['scheme_id']) ? (int)$data['scheme_id'] : 0,
            !empty($data['daily_limit']) ? (int)$data['daily_limit'] : 0
        ]);
        
        return $pdo->lastInsertId();
    }

    public static function update($id, $data) {
        $pdo = Database::getInstance()->getConnection();
        
        // Ensure columns exist (Auto-migration)
        self::ensureColumns();
        
        $fields = [];
        $values = [];
        $allowed = [
            'name', 'geo_region', 'language', 'category_id', 
            'keywords', 'keyword_count', 'promotion_info', 'status',
            'article_status', 'insert_image_count', 'insert_image_position',
            'image_source_type', 'custom_image_urls', 'media_category_id', 'auto_link', 'ai_model_id',
            'user_id', 'scheme_id', 'daily_limit'
        ];
        
        $intFields = [
            'category_id', 'keyword_count', 'status', 'article_status', 
            'insert_image_count', 'media_category_id', 'auto_link', 'ai_model_id',
            'user_id', 'scheme_id', 'daily_limit'
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                if (in_array($key, $intFields)) {
                    $value = (int)$value;
                }
                $fields[] = "`$key` = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE api_configs SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($values);
    }

    private static function ensureColumns() {
        $pdo = Database::getInstance()->getConnection();
        try {
            $columns = $pdo->query("SHOW COLUMNS FROM api_configs")->fetchAll(PDO::FETCH_COLUMN);
            
            if (!in_array('article_status', $columns)) {
                $pdo->exec("ALTER TABLE api_configs ADD COLUMN article_status INT DEFAULT 1");
            }
            if (!in_array('keyword_count', $columns)) {
                $pdo->exec("ALTER TABLE api_configs ADD COLUMN keyword_count INT DEFAULT 1");
            }
            if (!in_array('insert_image_count', $columns)) {
                $pdo->exec("ALTER TABLE api_configs ADD COLUMN insert_image_count INT DEFAULT 0");
            }
            if (!in_array('insert_image_position', $columns)) {
                $pdo->exec("ALTER TABLE api_configs ADD COLUMN insert_image_position VARCHAR(20) DEFAULT 'random'");
            }
            if (!in_array('image_source_type', $columns)) {
                $pdo->exec("ALTER TABLE api_configs ADD COLUMN image_source_type VARCHAR(50) DEFAULT 'picsum'");
            }
            if (!in_array('custom_image_urls', $columns)) {
                $pdo->exec("ALTER TABLE api_configs ADD COLUMN custom_image_urls TEXT NULL");
            }
            if (!in_array('media_category_id', $columns)) {
                $pdo->exec("ALTER TABLE api_configs ADD COLUMN media_category_id INT DEFAULT 0");
            }
            if (!in_array('auto_link', $columns)) {
                $pdo->exec("ALTER TABLE api_configs ADD COLUMN auto_link TINYINT(1) DEFAULT 0");
            }
            if (!in_array('ai_model_id', $columns)) {
                $pdo->exec("ALTER TABLE api_configs ADD COLUMN ai_model_id INT DEFAULT 0");
            }
            if (!in_array('user_id', $columns)) {
                $pdo->exec("ALTER TABLE api_configs ADD COLUMN user_id INT UNSIGNED NOT NULL DEFAULT 0");
                $pdo->exec("CREATE INDEX idx_user_id ON api_configs(user_id)");
            }
            if (!in_array('scheme_id', $columns)) {
                $pdo->exec("ALTER TABLE api_configs ADD COLUMN scheme_id INT UNSIGNED NOT NULL DEFAULT 0");
                $pdo->exec("CREATE INDEX idx_scheme_id ON api_configs(scheme_id)");
            }
            if (!in_array('daily_limit', $columns)) {
                $pdo->exec("ALTER TABLE api_configs ADD COLUMN daily_limit INT UNSIGNED NOT NULL DEFAULT 0");
            }
            
            // Fix for ai_schemes missing column if table exists (Cross-model safety)
            $schemesTableExists = $pdo->query("SHOW TABLES LIKE 'ai_schemes'")->rowCount() > 0;
            if ($schemesTableExists) {
                 $schemeColumns = $pdo->query("SHOW COLUMNS FROM ai_schemes")->fetchAll(PDO::FETCH_COLUMN);
                 if (!in_array('frozen_points', $schemeColumns)) {
                     // Check if cost_per_post exists to determine position, otherwise just add it
                     if (in_array('cost_per_post', $schemeColumns)) {
                        $pdo->exec("ALTER TABLE ai_schemes ADD COLUMN frozen_points INT UNSIGNED NOT NULL DEFAULT 0 AFTER cost_per_post");
                     } else {
                        $pdo->exec("ALTER TABLE ai_schemes ADD COLUMN frozen_points INT UNSIGNED NOT NULL DEFAULT 0");
                     }
                 }
            }

        } catch (Exception $e) {
            // Ignore if error, or log it
        }
    }

    public static function delete($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("DELETE FROM api_configs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function incrementCallCount($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE api_configs SET call_count = call_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function sumCallCounts() {
        $pdo = Database::getInstance()->getConnection();
        $sum = $pdo->query("SELECT SUM(call_count) FROM api_configs")->fetchColumn();
        return $sum ? (int)$sum : 0;
    }

    private static function syncOrphanSchemes($pdo) {
        // Find schemes that are not in api_configs
        // Note: user_id logic handles mapping scheme user_id to api_config user_id
        $sql = "SELECT s.* FROM ai_schemes s 
                LEFT JOIN api_configs ac ON s.id = ac.scheme_id 
                WHERE ac.id IS NULL";
        
        $stmt = $pdo->query($sql);
        $orphans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($orphans as $scheme) {
            $config = json_decode($scheme['config'], true);
            
            // Prepare data for create
            // We use direct insert or self::create logic but simpler to avoid recursion or complex dependency
            // Use existing create method to handle uuid/apikey generation
            
            // Map scheme config to api config fields
            $data = [
                'name' => $scheme['name'] . ' (User Scheme)',
                'geo_region' => $config['region'] ?? 'CN',
                'language' => $config['language'] ?? 'zh-CN',
                'keywords' => $config['keywords'] ?? '',
                'promotion_info' => $config['prompt'] ?? '',
                'status' => 0, // Disabled/Pending
                'user_id' => $scheme['user_id'],
                'scheme_id' => $scheme['id'],
                'daily_limit' => $scheme['daily_limit'] ?? 0,
                'keyword_count' => 1
            ];
            
            self::create($data);
        }
    }

    // Helpers
    private static function generateUuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private static function generateApiKey() {
        return bin2hex(random_bytes(32));
    }
}
