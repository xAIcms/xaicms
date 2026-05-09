<?php
$pageTitle = '隐私政策 - ' . ($settings['siteName'] ?? 'xAI');
include __DIR__ . '/partials/header.php';
?>

<div class="bg-gray-50 min-h-screen text-gray-700 pt-24 pb-12">
    <div class="max-w-4xl mx-auto px-6">
        <h1 class="text-4xl font-bold text-gray-900 mb-8 tracking-tight">隐私政策</h1>
        <div class="prose max-w-none prose-headings:text-gray-900 prose-a:text-indigo-600">
            <p class="text-sm text-gray-500 mb-8">生效日期：2025年12月1日</p>

            <h3>1. 引言</h3>
            <p>欢迎使用 <?php echo htmlspecialchars($settings['siteName'] ?? 'xAI'); ?>（以下简称"我们"或"本平台"）。我们非常重视您的隐私保护。本隐私政策旨在向您说明我们在您使用我们的服务时如何收集、使用、存储和保护您的个人信息。</p>

            <h3>2. 我们收集的信息</h3>
            <p>为了向您提供服务，我们可能会收集以下类型的信息：</p>
            <ul>
                <li><strong>用户内容：</strong> 您通过平台创建、上传或管理的内容。</li>
                <li><strong>设备与日志信息：</strong> 包括您的 IP 地址、浏览器类型、操作系统版本、访问日期和时间等。</li>
                <li><strong>Cookies：</strong> 我们使用 Cookies 来优化您的浏览体验，记住您的偏好设置。</li>
            </ul>

            <h3>3. 信息的使用</h3>
            <p>我们要收集的信息主要用于：</p>
            <ul>
                <li>提供内容管理服务。</li>
                <li>优化网站性能，提升用户体验。</li>
                <li>防止滥用和欺诈行为，保障平台安全。</li>
            </ul>

            <h3>4. 数据保留与删除</h3>
            <p><strong>重要承诺：</strong> 我们不会永久存储您的敏感内容。所有处理后的文件和源文件将在必要期限后从我们的服务器自动永久删除。我们仅保留必要的访问日志以满足法律合规要求。</p>

            <h3>5. 信息共享</h3>
            <p>我们承诺不会将您的个人信息出售、出租或交易给任何第三方。除非：</p>
            <ul>
                <li>获得您的明确同意。</li>
                <li>法律法规要求或政府机关的强制性命令。</li>
            </ul>

            <h3>6. 数据安全</h3>
            <p>我们采取行业标准的安全措施（如 SSL 加密传输）来保护您的数据安全。但请注意，互联网传输并非绝对安全，我们无法保证信息的绝对安全性。</p>

            <h3>7. 政策更新</h3>
            <p>我们可能会不时更新本隐私政策。更新后的政策将在本页面发布，并在生效日期处予以注明。建议您定期查看本页面以获取最新信息。</p>

            <h3>8. 联系我们</h3>
            <p>如果您对本隐私政策有任何疑问，请通过以下方式联系我们：</p>
            <p>Email: <?php echo htmlspecialchars($settings['contactEmail'] ?? 'contact@xAI.com'); ?></p>
        </div>
</div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>