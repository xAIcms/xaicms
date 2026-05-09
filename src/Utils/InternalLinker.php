<?php
// php_backend/src/Utils/InternalLinker.php

require_once __DIR__ . '/../Config/Database.php';

class InternalLinker {
    /**
     * 自动为内容中的关键词添加内链
     * 
     * @param string $content 文章内容
     * @param int|null $excludeArticleId 排除的当前文章ID（避免链接到自己）
     * @return string 处理后的内容
     */
    public static function autoLink($content, $excludeArticleId = null) {
        $pdo = Database::getInstance()->getConnection();
        
        // 1. 获取所有已发布的文章作为内链候选 (为了性能，可以限制数量或缓存)
        // 简单起见，这里获取最近的 500 篇文章的标题和链接
        // 也可以加入 Tag 作为内链关键词
        $sql = "SELECT id, title, slug FROM articles WHERE status = 1";
        if ($excludeArticleId) {
            $sql .= " AND id != " . (int)$excludeArticleId;
        }
        $sql .= " ORDER BY views DESC LIMIT 500"; // 优先链接热门文章
        
        $candidates = $pdo->query($sql)->fetchAll();
        
        // 按长度倒序排序，优先匹配长词
        usort($candidates, function($a, $b) {
            return mb_strlen($b['title']) - mb_strlen($a['title']);
        });

        // 2. 遍历替换
        // 注意：不要替换已经在链接中的文本，也不要替换 HTML 标签属性中的文本
        // 这是一个简化的实现，对于复杂的 HTML 结构可能不够完美，但对 Markdown/简单 HTML 有效
        
        // 提取现有的链接和标签，避免在其中替换
        // 使用占位符保护
        $protected = [];
        $i = 0;
        
        // 保护 HTML 标签 <...>
        $content = preg_replace_callback('/<[^>]+>/', function($matches) use (&$protected, &$i) {
            $key = "###PROTECTED_TAG_{$i}###";
            $protected[$key] = $matches[0];
            $i++;
            return $key;
        }, $content);
        
        // 保护 Markdown 链接 [text](url)
        $content = preg_replace_callback('/\[([^\]]+)\]\([^\)]+\)/', function($matches) use (&$protected, &$i) {
            $key = "###PROTECTED_MD_{$i}###";
            $protected[$key] = $matches[0];
            $i++;
            return $key;
        }, $content);

        // 3. 执行替换 (每个关键词只替换一次，避免过度优化)
        foreach ($candidates as $article) {
            $keyword = $article['title'];
            if (mb_strlen($keyword) < 2) continue; // 跳过太短的词
            
            // 链接地址
            $url = "/{$article['slug']}.html";
            $link = "<a href=\"{$url}\" title=\"{$keyword}\">{$keyword}</a>";
            
            // 仅替换第一次出现的关键词
            // 使用 preg_replace 限制 limit = 1
            $pattern = '/' . preg_quote($keyword, '/') . '/u';
            $content = preg_replace($pattern, $link, $content, 1);
        }

        // 4. 还原保护的内容
        if (!empty($protected)) {
            $content = str_replace(array_keys($protected), array_values($protected), $content);
        }

        return $content;
    }
}
