<?php
// public/check.php
// 用于诊断环境和伪静态配置

header('Content-Type: text/html; charset=utf-8');

echo "<h1>环境诊断工具</h1>";

// 1. 检查 PHP 版本
echo "<h2>1. PHP 版本</h2>";
echo "PHP Version: " . phpversion() . "<br>";

// 2. 检查伪静态 (Rewrite)
echo "<h2>2. 伪静态 (Rewrite) 检查</h2>";
echo "当前 REQUEST_URI: " . htmlspecialchars($_SERVER['REQUEST_URI']) . "<br>";
echo "当前 SCRIPT_NAME: " . htmlspecialchars($_SERVER['SCRIPT_NAME']) . "<br>";

if (strpos($_SERVER['REQUEST_URI'], '/check.php/') !== false) {
    echo "<p style='color:green'>✅ 伪静态/PathInfo 似乎工作正常 (能够访问到 check.php/xxx)</p>";
} else {
    echo "<p>请尝试访问 <a href='/check.php/test-rewrite'>/check.php/test-rewrite</a> 来测试 PathInfo。</p>";
    echo "<p>请尝试访问 <a href='/test-rewrite-rule'>/test-rewrite-rule</a> (如果不存该文件但能显示本页内容，说明重写到 index.php 成功 - 但当前是 check.php 所以此项需手动验证)</p>";
}

// 3. 数据库连接测试
echo "<h2>3. 数据库连接测试</h2>";
if (file_exists(__DIR__ . '/../config.php')) {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../src/Config/Database.php';
    
    try {
        $pdo = Database::getInstance()->getConnection();
        echo "<p style='color:green'>✅ 数据库连接成功</p>";
        
        // 4. 检查数据
        echo "<h2>4. 数据检查 (Slug)</h2>";
        
        // 文章
        echo "<h3>最新文章 (Top 5)</h3>";
        $stmt = $pdo->query("SELECT id, title, slug, status FROM articles ORDER BY id DESC LIMIT 5");
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($articles) {
            echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Title</th><th>Slug</th><th>Status</th><th>Link Preview</th></tr>";
            foreach ($articles as $a) {
                echo "<tr>";
                echo "<td>{$a['id']}</td>";
                echo "<td>" . htmlspecialchars($a['title']) . "</td>";
                echo "<td>" . htmlspecialchars($a['slug']) . "</td>";
                echo "<td>{$a['status']}</td>";
                echo "<td><a href='/{$a['slug']}.html' target='_blank'>/{$a['slug']}.html</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "暂无文章";
        }
        
        // 分类
        echo "<h3>分类列表</h3>";
        $stmt = $pdo->query("SELECT id, name, slug FROM categories LIMIT 10");
        $cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($cats) {
            echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Name</th><th>Slug</th><th>Link Preview</th></tr>";
            foreach ($cats as $c) {
                echo "<tr>";
                echo "<td>{$c['id']}</td>";
                echo "<td>" . htmlspecialchars($c['name']) . "</td>";
                echo "<td>" . htmlspecialchars($c['slug']) . "</td>";
                echo "<td><a href='/{$c['slug']}.html' target='_blank'>/{$c['slug']}.html</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "暂无分类";
        }
        
    } catch (Exception $e) {
        echo "<p style='color:red'>❌ 数据库连接失败: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>❌ config.php 不存在</p>";
}

echo "<hr>";
echo "<p>诊断结束。请删除此文件以保安全。</p>";
