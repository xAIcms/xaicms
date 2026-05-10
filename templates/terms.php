<?php
// templates/terms.php
$pageTitle = __('Terms of Service', '服务条款') . ' - ' . ($settings['siteName'] ?? 'xAI CMS');
include __DIR__ . '/partials/header.php';

$title = $settings['terms_title'] ?? __('Terms of Service', '服务条款');
$contentHtml = $settings['terms_content'] ?? '';

$defaultContent = '<p>' . sprintf(__('Welcome to %s. By using our services, you agree to these terms.', '欢迎使用 %s。使用我们的服务即表示您同意以下条款。'), htmlspecialchars($settings['siteName'] ?? 'xAI CMS')) . '</p>';
?>

<div class="bg-gray-50 min-h-screen text-gray-700 pt-24 pb-12">
    <div class="max-w-4xl mx-auto px-6">
        <h1 class="text-4xl font-bold text-gray-900 mb-8 tracking-tight"><?php echo htmlspecialchars($title); ?></h1>
        <div class="prose max-w-none prose-headings:text-gray-900 prose-a:text-indigo-600">
            <?php if ($contentHtml): ?>
                <?php echo $contentHtml; ?>
            <?php else: ?>
                <?php echo $defaultContent; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
