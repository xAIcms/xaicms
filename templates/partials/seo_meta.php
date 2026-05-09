<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle ?? $settings['siteName']); ?></title>
<?php
$finalDescription = !empty($pageDescription) ? $pageDescription : ($settings['siteDescription'] ?? '');
$finalKeywords = !empty($pageKeywords) ? $pageKeywords : ($settings['siteKeywords'] ?? '');
?>
<meta name="description" content="<?php echo htmlspecialchars($finalDescription); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($finalKeywords); ?>">

<?php
// Adaptive Domain Logic for Meta Tags
$protocol = 'http';
if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
    $protocol = 'https';
}

$adaptiveSiteUrl = $settings['siteUrl'] ?? 'http://localhost';
if (isset($_SERVER['HTTP_HOST'])) {
     $adaptiveSiteUrl = $protocol . '://' . $_SERVER['HTTP_HOST'];
}
$adaptiveSiteUrl = rtrim($adaptiveSiteUrl, '/');
?>

<!-- Open Graph / Social -->
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle ?? $settings['siteName']); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($pageDescription ?? $settings['siteDescription'] ?? ''); ?>">
<?php 
$ogImage = (isset($article) && is_array($article) && isset($article['cover_image']) && !empty($article['cover_image'])) ? $article['cover_image'] : '/assets/images/logo.png';
if (strpos($ogImage, 'http') !== 0) {
    // Ensure image path starts with slash
    $ogImage = '/' . ltrim($ogImage, '/');
    $ogImage = $adaptiveSiteUrl . $ogImage;
}
?>
<meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($currentUrl ?? ($adaptiveSiteUrl . $_SERVER['REQUEST_URI'])); ?>">
<meta property="og:site_name" content="<?php echo htmlspecialchars($settings['siteName']); ?>">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle ?? $settings['siteName']); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription ?? $settings['siteDescription'] ?? ''); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage); ?>">

<!-- Canonical -->
<link rel="canonical" href="<?php echo htmlspecialchars($currentUrl ?? ($adaptiveSiteUrl . $_SERVER['REQUEST_URI'])); ?>">

<!-- GEO Tags -->
<?php if (!empty($settings['geoRegion'])): ?>
<meta name="geo.region" content="<?php echo htmlspecialchars($settings['geoRegion']); ?>">
<?php endif; ?>
<?php if (!empty($settings['geoPlacename'])): ?>
<meta name="geo.placename" content="<?php echo htmlspecialchars($settings['geoPlacename']); ?>">
<?php endif; ?>
<?php if (!empty($settings['geoPosition'])): ?>
<meta name="geo.position" content="<?php echo htmlspecialchars($settings['geoPosition']); ?>">
<meta name="ICBM" content="<?php echo htmlspecialchars($settings['geoPosition']); ?>">
<?php endif; ?>

<!-- Schema.org JSON-LD -->
<?php
$schema = [
    "@context" => "https://schema.org",
    "@type" => "WebSite",
    "name" => $settings['siteName'],
    "url" => $adaptiveSiteUrl
];

if (isset($article) && is_array($article)) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "Article",
        "headline" => $article['title'] ?? '',
        "image" => $ogImage,
        "datePublished" => $article['published_at'] ?? date('c'),
        "dateModified" => $article['updated_at'] ?? $article['published_at'] ?? date('c'),
        "author" => [
            "@type" => "Person",
            "name" => "xAI Team"
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => $settings['siteName'] ?? 'xAI',
            "logo" => [
                "@type" => "ImageObject",
                "url" => $adaptiveSiteUrl . '/assets/images/logo.png'
            ]
        ],
        "description" => $article['summary'] ?? mb_substr(strip_tags((string)($article['content'] ?? '')), 0, 160)
    ];
} elseif (isset($category) && is_array($category)) {
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "CollectionPage",
        "name" => $category['name'],
        "description" => $category['description'] ?? "Articles in " . $category['name'],
        "url" => $currentUrl ?? ($adaptiveSiteUrl . $_SERVER['REQUEST_URI'])
    ];
}
?>
<script type="application/ld+json">
<?php echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>