<?php
// templates/about.php
$pageTitle = __('About', '关于我们') . ' - ' . ($settings['siteName'] ?? 'xAI CMS');
include __DIR__ . '/partials/header.php';

$heroTitle = $settings['about_hero_title'] ?? __('About Us', '关于我们');
$heroDesc = $settings['about_hero_desc'] ?? '';
$contentHtml = $settings['about_content'] ?? '';
$contactEmail = $settings['about_contact_email'] ?? ($settings['adminEmail'] ?? '');
$contactPhone = $settings['about_contact_phone'] ?? '';
$contactAddress = $settings['about_contact_address'] ?? '';
?>

<div class="max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8">
    <div class="text-center mb-16">
        <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
            <?php echo htmlspecialchars($heroTitle); ?>
        </h1>
        <?php if ($heroDesc): ?>
        <p class="mt-5 max-w-xl mx-auto text-xl text-gray-500">
            <?php echo htmlspecialchars($heroDesc); ?>
        </p>
        <?php endif; ?>
    </div>

    <?php if ($contentHtml): ?>
    <div class="grid grid-cols-1 <?php echo ($contactEmail || $contactPhone || $contactAddress) ? 'lg:grid-cols-3' : ''; ?> gap-12 items-start">
        <div class="<?php echo ($contactEmail || $contactPhone || $contactAddress) ? 'lg:col-span-2' : ''; ?>">
            <div class="prose prose-lg text-gray-600 max-w-none">
                <?php echo $contentHtml; // HTML from trusted admin input ?>
            </div>
        </div>

        <?php if ($contactEmail || $contactPhone || $contactAddress): ?>
        <div class="space-y-8">
            <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200">
                <h3 class="text-xl font-bold mb-6 flex items-center text-gray-900">
                    <i class="bi bi-person-lines-fill mr-2 text-indigo-600"></i> <?php echo __('Contact', '联系方式'); ?>
                </h3>
                <ul class="space-y-6">
                    <?php if ($contactEmail): ?>
                    <li class="flex items-start">
                        <div class="bg-indigo-50 p-3 rounded-xl mr-4">
                            <i class="bi bi-envelope text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900"><?php echo __('Email', '邮箱'); ?></p>
                            <a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>" class="text-gray-600 mt-1 hover:text-indigo-600 transition-colors block">
                                <?php echo htmlspecialchars($contactEmail); ?>
                            </a>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if ($contactPhone): ?>
                    <li class="flex items-start">
                        <div class="bg-indigo-50 p-3 rounded-xl mr-4">
                            <i class="bi bi-telephone text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900"><?php echo __('Phone', '电话'); ?></p>
                            <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($contactPhone); ?></p>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if ($contactAddress): ?>
                    <li class="flex items-start">
                        <div class="bg-indigo-50 p-3 rounded-xl mr-4">
                            <i class="bi bi-geo-alt text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900"><?php echo __('Address', '地址'); ?></p>
                            <p class="text-gray-600 mt-1"><?php echo nl2br(htmlspecialchars($contactAddress)); ?></p>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-16 text-gray-500">
        <i class="bi bi-pencil-square text-5xl mb-4 block text-gray-300"></i>
        <p class="text-lg"><?php echo __('No content yet. Edit this page in admin settings.', '暂无内容，请在后台设置中编辑此页面。'); ?></p>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
