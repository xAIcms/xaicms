<?php
// php_backend/src/Models/Article.php

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Utils/SlugGenerator.php';

class Article {
    // --- Frontend Methods ---

    public static function getBySlug($slug) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = ? AND status = 1 LIMIT 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public static function getLatest($limit = 10, $offset = 0) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT a.*, c.name as category_name 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            WHERE a.status = 1 
            ORDER BY a.published_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getPopular($limit = 5) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT a.*, c.name as category_name 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            WHERE a.status = 1 
            ORDER BY a.views DESC, a.published_at DESC 
            LIMIT ?
        ");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public static function countPublished() {
        $pdo = Database::getInstance()->getConnection();
        return $pdo->query("SELECT COUNT(*) FROM articles WHERE status = 1")->fetchColumn();
    }

    public static function sumViews() {
        $pdo = Database::getInstance()->getConnection();
        // sum(views) might return null if no rows, cast to int
        $sum = $pdo->query("SELECT SUM(views) FROM articles")->fetchColumn();
        return $sum ? (int)$sum : 0;
    }

    public static function incrementViews($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function getByCategory($categoryId, $limit = 10, $offset = 0) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE category_id = ? AND status = 1 ORDER BY published_at DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countByCategory($categoryId) {
        // Use cached count in categories table if possible, but here we query for consistency with status=1
        // Actually, for performance on high volume, we should rely on the cached count in categories table,
        // but that count includes all statuses? No, usually cached count should only reflect published or all?
        // Let's stick to COUNT(*) with index for now as it's safer for status=1 check.
        // The index idx_category_status (category_id, status, published_at) should make this fast.
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ? AND status = 1");
        $stmt->execute([$categoryId]);
        return $stmt->fetchColumn();
    }

    public static function getByTag($tagId, $limit = 10, $offset = 0) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug
            FROM articles a
            JOIN article_tags at ON a.id = at.article_id
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE at.tag_id = ? AND a.status = 1
            ORDER BY a.published_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $tagId, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countByTag($tagId) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM articles a
            JOIN article_tags at ON a.id = at.article_id
            WHERE at.tag_id = ? AND a.status = 1
        ");
        $stmt->execute([$tagId]);
        return $stmt->fetchColumn();
    }

    public static function search($keyword, $limit = 10, $offset = 0) {
        $pdo = Database::getInstance()->getConnection();
        
        try {
            // 尝试使用全文检索 (MySQL Full-Text Search with ngram parser)
            // 注意：需要在数据库中添加 FULLTEXT 索引: 
            // ALTER TABLE articles ADD FULLTEXT INDEX idx_fulltext_title_summary (title, summary) WITH PARSER ngram;
            
            // 使用 Boolean Mode
            $stmt = $pdo->prepare("
                SELECT a.*, c.name as category_name, c.slug as category_slug 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE MATCH(a.title, a.summary) AGAINST(? IN BOOLEAN MODE) 
                AND a.status = 1 
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $keyword);
            $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(3, (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            // 如果全文索引不存在，回退到 LIKE 查询
            $keywordLike = "%$keyword%";
            $stmt = $pdo->prepare("
                SELECT a.*, c.name as category_name, c.slug as category_slug 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                WHERE (a.title LIKE ? OR a.summary LIKE ?) AND a.status = 1 
                ORDER BY a.published_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->bindValue(1, $keywordLike);
            $stmt->bindValue(2, $keywordLike);
            $stmt->bindValue(3, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(4, (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    public static function countSearch($keyword) {
        $pdo = Database::getInstance()->getConnection();
        
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE MATCH(title, summary) AGAINST(? IN BOOLEAN MODE) AND status = 1");
            $stmt->execute([$keyword]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            $keyword = "%$keyword%";
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE (title LIKE ? OR summary LIKE ?) AND status = 1");
            $stmt->execute([$keyword, $keyword]);
            return $stmt->fetchColumn();
        }
    }

    // --- Admin Methods ---

    public static function getAll($limit = 20, $offset = 0) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT a.*, c.name as category_name 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            ORDER BY a.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function countAll() {
        $pdo = Database::getInstance()->getConnection();
        return $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    }

    public static function find($id) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getRelated($articleId, $categoryId, $limit = 3) {
        $pdo = Database::getInstance()->getConnection();
        
        // 简单的相关文章推荐逻辑：同一分类下的其他最新文章
        // 进阶逻辑可以是：基于标签重合度 (Tag-based recommendation)
        $stmt = $pdo->prepare("
            SELECT id, title, slug, cover_image, summary, published_at, views
            FROM articles 
            WHERE category_id = ? 
            AND id != ? 
            AND status = 1 
            ORDER BY published_at DESC 
            LIMIT ?
        ");
        
        $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(2, $articleId, PDO::PARAM_INT);
        $stmt->bindValue(3, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        
        // 如果同分类文章不足，可以尝试补充（暂不实现复杂补充逻辑）
        return $results;
    }

    public static function create($data) {
        $pdo = Database::getInstance()->getConnection();
        
        // Generate UUID if not provided
        if (empty($data['uuid'])) {
            $data['uuid'] = self::generateUuid();
        }

        // Ensure slug is valid and unique
        // If slug is provided (e.g. from AI or user input), use it as base; otherwise use title
        $baseSlug = !empty($data['slug']) ? $data['slug'] : $data['title'];
        $data['slug'] = self::generateSlug($baseSlug);
        
        // Auto Internal Links
        if (isset($data['auto_link']) && $data['auto_link']) {
            require_once __DIR__ . '/../Utils/InternalLinker.php';
            $data['content'] = InternalLinker::autoLink($data['content']);
        }
        
        $sql = "INSERT INTO articles (
            uuid, category_id, title, slug, summary, content, cover_image, 
            geo_region, language, status, seo_title, seo_description, seo_keywords, published_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['uuid'],
            $data['category_id'] ?? 0,
            $data['title'],
            $data['slug'],
            $data['summary'] ?? '',
            $data['content'] ?? '',
            $data['cover_image'] ?? '',
            $data['geo_region'] ?? 'CN',
            $data['language'] ?? 'zh-CN',
            $data['status'] ?? 0,
            $data['seo_title'] ?? '',
            $data['seo_description'] ?? '',
            $data['seo_keywords'] ?? '',
            $data['published_at'] ?? ($data['status'] == 1 ? date('Y-m-d H:i:s') : null)
        ]);
        
        $id = $pdo->lastInsertId();

        // Update Category Count
        if (!empty($data['category_id'])) {
            self::updateCategoryCount($data['category_id'], 1);
        }

        return $id;
    }

    public static function update($id, $data) {
        $pdo = Database::getInstance()->getConnection();
        
        // Auto Internal Links
        if (isset($data['auto_link']) && $data['auto_link']) {
            require_once __DIR__ . '/../Utils/InternalLinker.php';
            $data['content'] = InternalLinker::autoLink($data['content'], $id);
        }
        
        // Get old data to check for category change
        $oldArticle = self::find($id);
        
        $fields = [];
        $values = [];
        
        // Allowed fields to update
        $allowed = ['category_id', 'title', 'slug', 'summary', 'content', 'cover_image', 'geo_region', 'language', 'status', 'seo_title', 'seo_description', 'seo_keywords', 'published_at'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $fields[] = "`$key` = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE articles SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($values);

        // Update Category Counts if changed
        if ($result && isset($data['category_id']) && $oldArticle['category_id'] != $data['category_id']) {
            if ($oldArticle['category_id'] > 0) {
                self::updateCategoryCount($oldArticle['category_id'], -1);
            }
            if ($data['category_id'] > 0) {
                self::updateCategoryCount($data['category_id'], 1);
            }
        }

        return $result;
    }

    // --- Tag Methods ---

    public static function getTags($articleId) {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT t.* 
            FROM tags t
            JOIN article_tags at ON t.id = at.tag_id
            WHERE at.article_id = ?
        ");
        $stmt->execute([$articleId]);
        return $stmt->fetchAll();
    }

    public static function syncTags($articleId, $tags) {
        $pdo = Database::getInstance()->getConnection();
        
        // Get old tags to update counts later
        $oldTags = self::getTags($articleId);
        $oldTagIds = array_column($oldTags, 'id');

        // $tags can be an array of tag names.
        if (empty($tags)) {
            // Remove all tags
            $stmt = $pdo->prepare("DELETE FROM article_tags WHERE article_id = ?");
            $stmt->execute([$articleId]);
            
            // Decrement counts for old tags
            foreach ($oldTagIds as $oldTid) {
                self::updateTagCount($oldTid, -1);
            }
            return;
        }

        $tagIds = [];
        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if (empty($tagName)) continue;

            // Check if tag exists
            $stmt = $pdo->prepare("SELECT id FROM tags WHERE name = ?");
            $stmt->execute([$tagName]);
            $tagId = $stmt->fetchColumn();

            if (!$tagId) {
                // Create new tag
                $uuid = self::generateUuid();
                $slug = self::generateSlug($tagName);
                
                // Handle slug collision for tags (simple append)
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM tags WHERE slug = ?");
                $stmt->execute([$slug]);
                if ($stmt->fetchColumn() > 0) {
                    $slug .= '-' . time();
                }

                $stmt = $pdo->prepare("INSERT INTO tags (uuid, name, slug) VALUES (?, ?, ?)");
                $stmt->execute([$uuid, $tagName, $slug]);
                $tagId = $pdo->lastInsertId();
            }
            $tagIds[] = $tagId;
        }

        // Identify added and removed tags for count updates
        $addedTagIds = array_diff($tagIds, $oldTagIds);
        $removedTagIds = array_diff($oldTagIds, $tagIds);

        // Sync: Remove old relations and add new ones
        $pdo->beginTransaction();
        try {
            // Delete existing
            $stmt = $pdo->prepare("DELETE FROM article_tags WHERE article_id = ?");
            $stmt->execute([$articleId]);

            // Insert new
            if (!empty($tagIds)) {
                $placeholders = [];
                $values = [];
                foreach ($tagIds as $tid) {
                    $placeholders[] = "(?, ?)";
                    $values[] = $articleId;
                    $values[] = $tid;
                }
                $sql = "INSERT INTO article_tags (article_id, tag_id) VALUES " . implode(', ', $placeholders);
                $stmt = $pdo->prepare($sql);
                $stmt->execute($values);
            }
            
            // Update counts
            foreach ($addedTagIds as $tid) self::updateTagCount($tid, 1);
            foreach ($removedTagIds as $tid) self::updateTagCount($tid, -1);
            
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function delete($id) {
        $pdo = Database::getInstance()->getConnection();
        
        $article = self::find($id);
        if (!$article) return false;

        // Get tags to decrement count
        $tags = self::getTags($id);

        $pdo->beginTransaction();
        try {
            // Delete article_tags
            $stmt = $pdo->prepare("DELETE FROM article_tags WHERE article_id = ?");
            $stmt->execute([$id]);

            // Delete article
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->execute([$id]);
            
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

        // Update Category Count
        if ($article['category_id'] > 0) {
            self::updateCategoryCount($article['category_id'], -1);
        }

        // Update Tag Counts
        foreach ($tags as $tag) {
            self::updateTagCount($tag['id'], -1);
        }

        return true;
    }

    // Helper to update category count
    private static function updateCategoryCount($categoryId, $delta) {
        $pdo = Database::getInstance()->getConnection();
        $sql = "UPDATE categories SET article_count = article_count + ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$delta, $categoryId]);
    }

    // Helper to update tag count
    private static function updateTagCount($tagId, $delta) {
        $pdo = Database::getInstance()->getConnection();
        $sql = "UPDATE tags SET article_count = article_count + ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$delta, $tagId]);
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

    private static function generateSlug($string) {
        $slug = SlugGenerator::generate($string);
        
        if (empty($slug)) {
            $slug = 'article-' . time() . '-' . mt_rand(1000, 9999);
        }
        
        // Ensure slug is unique
        $pdo = Database::getInstance()->getConnection();
        $originalSlug = $slug;
        $count = 1;
        
        while (true) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetchColumn() == 0) {
                break;
            }
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        
        return $slug;
    }
}
