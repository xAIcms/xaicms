<?php
// src/Utils/SitemapGenerator.php

require_once __DIR__ . '/../Models/Article.php';
require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/../Models/Tag.php';
require_once __DIR__ . '/../Models/Settings.php';

class SitemapGenerator {
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
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Homepage
        $xml .= self::createUrl($siteUrl . '/', date('Y-m-d'), '1.0', 'daily');

        // Static Pages
        $xml .= self::createUrl($siteUrl . '/news', date('Y-m-d'), '0.9', 'daily');
        $xml .= self::createUrl($siteUrl . '/categories', date('Y-m-d'), '0.8', 'weekly');
        $xml .= self::createUrl($siteUrl . '/about', date('Y-m-d'), '0.7', 'monthly');
        
        // Categories
        $categories = Category::getAll();
        foreach ($categories as $category) {
            $xml .= self::createUrl($siteUrl . '/' . $category['slug'] . '.html', null, '0.8', 'weekly');
        }
        
        // Tags
        $tags = Tag::getAll();
        foreach ($tags as $tag) {
            $xml .= self::createUrl($siteUrl . '/tag/' . $tag['slug'] . '.html', null, '0.6', 'weekly');
        }
        
        // Articles
        // Get all published articles (limit to recent 1000 or similar if needed, but for now all)
        // Article::getAll is for admin (paginated), let's use a custom query or modify Article model if needed.
        // For sitemap we need all active articles. 
        // Let's add a method or just use raw query here for simplicity or add a helper.
        // Actually Article::getLatest takes limit/offset. We might need a getAllPublishedUrls method.
        // For now, let's fetch latest 2000 to be safe.
        $articles = Article::getLatest(2000); 
        foreach ($articles as $article) {
            $lastMod = date('Y-m-d', strtotime($article['updated_at'] ?: $article['published_at']));
            $xml .= self::createUrl($siteUrl . '/news/' . $article['slug'] . '.html', $lastMod, '0.9', 'daily');
        }
        
        $xml .= '</urlset>';
        return $xml;
    }
    
    private static function createUrl($loc, $lastmod = null, $priority = '0.5', $changefreq = 'weekly') {
        $str = '<url>';
        $str .= '<loc>' . htmlspecialchars($loc) . '</loc>';
        if ($lastmod) {
            $str .= '<lastmod>' . $lastmod . '</lastmod>';
        }
        $str .= '<changefreq>' . $changefreq . '</changefreq>';
        $str .= '<priority>' . $priority . '</priority>';
        $str .= '</url>';
        return $str;
    }
}
