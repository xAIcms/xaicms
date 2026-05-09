<?php
// templates/partials/header.php
if (!isset($categories)) {
    $categories = Category::getAll();
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($settings['language'] ?? 'zh-CN'); ?>" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? $settings['siteName']); ?></title>
    
    <!-- Favicon -->
    <?php 
    $favicon = !empty($settings['siteFavicon']) ? $settings['siteFavicon'] : '/favicon.svg';
    $faviconType = (strpos($favicon, '.svg') !== false) ? 'image/svg+xml' : 'image/x-icon';
    ?>
    <link rel="icon" href="<?php echo htmlspecialchars($favicon); ?>" type="<?php echo $faviconType; ?>">
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($favicon); ?>" type="<?php echo $faviconType; ?>">
    
    <!-- Unified SEO/Meta Tags -->
    <?php include __DIR__ . '/seo_meta.php'; ?>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="/assets/css/custom.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: '#4f46e5', // Corporate indigo color
                        dark: '#1f2937',
                    }
                }
            }
        }
    </script>
    
    <!-- Landing Page Assets -->
    <script src="/assets/vendor/lucide/lucide.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons (Kept for compatibility with existing templates) -->
    <link href="/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
    
    
    
    <!-- Custom CSS -->
    <?php if (!empty($settings['customCss'])): ?>
    <style>
        <?php echo $settings['customCss']; ?>
    </style>
    <?php endif; ?>
</head>
<body class="bg-gray-50 font-sans text-gray-900 selection:bg-indigo-500 selection:text-white flex flex-col min-h-screen">

<?php include __DIR__ . '/navbar.php'; ?>

