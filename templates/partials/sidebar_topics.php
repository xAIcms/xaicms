<?php
// templates/partials/sidebar_topics.php
// Expected variables: None (fetches its own data), or optional $limit

$limit = isset($limit) ? $limit : 15;
$sidebarTags = Tag::getPopular($limit);
?>
<div class="bg-white rounded-2xl p-5 shadow-lg border border-gray-200">
    <h3 class="font-bold text-gray-900 mb-4 flex items-center text-sm uppercase tracking-wider">
        <i class="bi bi-hash w-4 h-4 mr-2 text-indigo-600"></i>
        热门话题
    </h3>
    <div class="flex flex-wrap gap-2">
        <?php if (empty($sidebarTags)): ?>
            <p class="text-xs text-gray-500 italic">暂无热门话题</p>
        <?php else: ?>
            <?php foreach ($sidebarTags as $tag): ?>
                <a href="/tag/<?php echo htmlspecialchars($tag['slug']); ?>.html" class="px-3 py-1 bg-gray-100 hover:bg-indigo-100 text-gray-700 hover:text-indigo-600 text-xs font-medium rounded-lg transition-colors border border-gray-200 hover:border-indigo-300 no-underline">
                    #<?php echo htmlspecialchars($tag['name']); ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
