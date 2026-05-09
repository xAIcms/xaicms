<?php
$pageTitle = '服务条款 - ' . ($settings['siteName'] ?? 'xAI');
include __DIR__ . '/partials/header.php';
?>

<div class="bg-gray-50 min-h-screen text-gray-700 pt-24 pb-12">
    <div class="max-w-4xl mx-auto px-6">
        <h1 class="text-4xl font-bold text-gray-900 mb-8 tracking-tight">服务条款</h1>
        <div class="prose max-w-none prose-headings:text-gray-900 prose-a:text-indigo-600">
            <p class="text-sm text-gray-500 mb-8">生效日期：2025年12月1日</p>

            <h3>1. 接受条款</h3>
            <p>欢迎访问 <?php echo htmlspecialchars($settings['siteName'] ?? 'xAI'); ?>。通过访问或使用本网站，即表示您同意遵守本服务条款及所有适用的法律法规。如果您不同意这些条款，请立即停止使用本网站。</p>

            <h3>2. 服务说明</h3>
            <p><?php echo htmlspecialchars($settings['siteName'] ?? 'xAI'); ?> 提供在线内容管理服务，主要功能包括内容发布、SEO优化等。我们保留随时修改、暂停或终止服务的权利，恕不另行通知。</p>

            <h3>3. 用户行为规范</h3>
            <p>您同意在使用本服务时遵守以下规定：</p>
            <ul>
                <li>不得利用本服务处理非法、色情、暴力或侵犯他人版权的内容。</li>
                <li>不得尝试攻击、破坏或干扰本服务的正常运行。</li>
                <li>不得使用自动化脚本或爬虫程序滥用本服务接口。</li>
            </ul>

            <h3>4. 知识产权</h3>
            <p>本网站的所有内容（包括但不限于文字、代码、界面设计）均归 <?php echo htmlspecialchars($settings['siteName'] ?? 'xAI'); ?> 所有。用户上传的内容版权归原作者所有，我们不主张对用户内容的任何权利。</p>

            <h3>5. 免责声明</h3>
            <p>本服务按"原样"提供，不包含任何明示或暗示的保证。我们不保证服务不会中断或没有错误。对于因使用本服务而导致的任何直接或间接损失，<?php echo htmlspecialchars($settings['siteName'] ?? 'xAI'); ?> 不承担任何责任。</p>
            <p><strong>特别声明：</strong> 本站与 Google、Gemini 或其他第三方服务无任何关联。本站提供的工具仅供学习和个人使用。</p>

            <h3>6. 第三方链接</h3>
            <p>本网站可能包含指向第三方网站的链接。这些链接仅为方便用户提供，我们对第三方网站的内容或隐私做法不承担责任。</p>

            <h3>7. 适用法律</h3>
            <p>本条款受相关法律管辖。如发生争议，双方应友好协商解决。</p>

            <h3>8. 条款修改</h3>
            <p>我们有权随时修改本服务条款。修改后的条款一经发布即生效。您继续使用本服务即视为接受修改后的条款。</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>