<?php
// src/Utils/RssGenerator.php

require_once __DIR__ . '/../Models/Article.php';
require_once __DIR__ . '/../Models/Settings.php';

class RssGenerator {
    public static function generate() {
        $settings = Settings::getAll();
        
        // Adaptive Domain Logic
        $protocol = 'http';
        if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            $protocol = 'https';
        }
        
        if (isset($_SERVER['HTTP_HOST'])) {
             $siteUrl = $protocol . '://' . $_SERVER['HTTP_HOST'];
        } else {
             $siteUrl = $settings['siteUrl'] ?? 'http://localhost';
        }
        $siteUrl = rtrim($siteUrl, '/');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
        $xml .= '<channel>';
        
        // Channel Info
        $xml .= '<title>' . htmlspecialchars($settings['siteName']) . '</title>';
        $xml .= '<link>' . htmlspecialchars($siteUrl) . '</link>';
        $xml .= '<description>' . htmlspecialchars($settings['siteDescription'] ?? 'Latest articles') . '</description>';
        $xml .= '<language>' . htmlspecialchars($settings['language'] ?? 'zh-CN') . '</language>';
        $xml .= '<atom:link href="' . $siteUrl . '/rss.xml" rel="self" type="application/rss+xml" />';
        
        // Items
        $articles = Article::getLatest(20); // Limit to recent 20
        foreach ($articles as $article) {
            $link = $siteUrl . '/' . $article['slug'] . '.html';
            
            $xml .= '<item>';
            $xml .= '<title>' . htmlspecialchars($article['title']) . '</title>';
            $xml .= '<link>' . $link . '</link>';
            $xml .= '<guid isPermaLink="true">' . $link . '</guid>';
            $xml .= '<pubDate>' . date(DATE_RSS, strtotime($article['published_at'])) . '</pubDate>';
            $xml .= '<description><![CDATA[' . ($article['summary'] ?? mb_substr(strip_tags($article['content']), 0, 200) . '...') . ']]></description>';
            
            if (!empty($article['cover_image'])) {
                // Ensure absolute URL for enclosure
                $imageUrl = $article['cover_image'];
                if (strpos($imageUrl, 'http') !== 0) {
                    $imageUrl = $siteUrl . '/' . ltrim($imageUrl, '/');
                }
                // Try to guess mime type or default to jpeg
                $mime = 'image/jpeg';
                if (strpos($imageUrl, '.png') !== false) $mime = 'image/png';
                if (strpos($imageUrl, '.webp') !== false) $mime = 'image/webp';
                
                $xml .= '<enclosure url="' . htmlspecialchars($imageUrl) . '" type="' . $mime . '" />';
            }
            
            $xml .= '</item>';
        }
        
        $xml .= '</channel>';
        $xml .= '</rss>';
        
        return $xml;
    }
}
