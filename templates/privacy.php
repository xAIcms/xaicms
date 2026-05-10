<?php
// templates/privacy.php
$pageTitle = __('Privacy Policy', '隐私政策') . ' - ' . ($settings['siteName'] ?? 'xAI CMS');
include __DIR__ . '/partials/header.php';

$title = $settings['privacy_title'] ?? __('Privacy Policy', '隐私政策');
$contentHtml = $settings['privacy_content'] ?? '';

$defaultContent = '<p>' . htmlspecialchars($settings['siteName'] ?? 'xAI CMS') . ' ' . __('takes your privacy seriously.', '重视您的隐私保护。') . '</p>'
    . '<p>' . __('This privacy policy describes how we collect, use, and protect your information.', '本隐私政策说明我们如何收集、使用和保护您的信息。') . '</p>';
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
