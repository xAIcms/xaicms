<?php
// templates/faq.php
$pageTitle = $pageTitle ?? ('常见问题 - ' . ($settings['siteName'] ?? 'xAI CMS'));
include __DIR__ . '/partials/header.php';
?>

<section class="py-16 bg-white min-h-screen">
    <div class="max-w-3xl mx-auto px-6">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">常见问题</h1>
        <p class="text-gray-500 mb-12">关于 xAI CMS 的常见问题和解答</p>

        <div class="space-y-8">
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">xAI CMS 是免费的吗？</h3>
                <p class="text-gray-600">是的，xAI CMS 基于 MIT 协议开源，自托管完全免费。AI 功能需要你自己配置 API Key（如 DeepSeek），按 API 用量付费。我们也提供付费托管服务。</p>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">需要什么服务器配置？</h3>
                <p class="text-gray-600">最低 1核2G 云服务器即可运行。推荐 2核4G 以获得更好的AI生成体验。支持阿里云、腾讯云、华为云及海外 VPS。</p>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">支持哪些 AI 模型？</h3>
                <p class="text-gray-600">支持所有 OpenAI 兼容 API：DeepSeek（默认）、OpenAI GPT系列、Claude、通义千问、火山引擎等。后台可配置多个模型切换使用。</p>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">AI 生成内容的成本是多少？</h3>
                <p class="text-gray-600">使用 DeepSeek Flash 模型，生成一篇约1000字的文章成本不到 0.1 元人民币。具体费用取决于你选择的 API 服务商和模型。</p>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">如何部署？</h3>
                <p class="text-gray-600">一行命令：<code>git clone https://github.com/xAIcms/xaicms.git && cd xaicms && docker-compose up -d</code>。支持 Docker、宝塔面板、手动 Nginx 部署。</p>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">可以商用吗？</h3>
                <p class="text-gray-600">可以。MIT 协议允许任意商业使用，包括修改代码、二次开发、用于客户项目。</p>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">免费版和 Pro 版有什么区别？</h3>
                <p class="text-gray-600">免费版支持基础文章管理和有限的AI调用。Pro 版解锁插件系统、模板市场、SEO工具包、API访问等高级功能。详见后台"升级计划"页面。</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
