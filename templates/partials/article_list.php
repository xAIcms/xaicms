<div class="space-y-6">
    <?php foreach ($articles as $article): ?>
        <article class="group flex flex-col md:flex-row gap-6 bg-white p-4 rounded-2xl shadow-lg border border-gray-200 hover:border-indigo-300 transition-all duration-300">
            <a href="/<?php echo htmlspecialchars($article['slug']); ?>.html" class="md:w-60 flex-shrink-0 relative overflow-hidden rounded-xl aspect-video">
                <img src="<?php echo htmlspecialchars(!empty($article['cover_image']) ? $article['cover_image'] : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=800'); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
            </a>
            
            <div class="flex-1 flex flex-col justify-center min-w-0">
                <div class="flex items-center space-x-2 text-xs text-gray-500 mb-2">
                    <?php if (!empty($article['category_name'])): ?>
                        <span class="font-medium text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded border border-indigo-200">
                            <?php echo htmlspecialchars($article['category_name']); ?>
                        </span>
                        <span class="text-gray-400">•</span>
                    <?php endif; ?>
                    
                    <?php 
                    $tags = Article::getTags($article['id']);
                    if (!empty($tags)): 
                        foreach (array_slice($tags, 0, 3) as $tag):
                    ?>
                        <a href="/tag/<?php echo htmlspecialchars($tag['slug']); ?>.html" class="text-gray-600 hover:text-indigo-600 transition-colors">#<?php echo htmlspecialchars($tag['name']); ?></a>
                    <?php 
                        endforeach;
                    ?>
                        <span class="text-gray-400">•</span>
                    <?php endif; ?>

                    <span><?php echo date('Y-m-d', strtotime($article['published_at'])); ?></span>
                    <span class="text-gray-400">•</span>
                    <span><i class="bi bi-eye"></i> <?php echo $article['views']; ?></span>
                </div>
                
                <a href="/<?php echo htmlspecialchars($article['slug']); ?>.html">
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-indigo-600 transition-colors leading-snug">
                        <?php echo htmlspecialchars($article['title']); ?>
                    </h3>
                </a>
                
                <p class="text-gray-600 text-sm line-clamp-2 mb-4 leading-relaxed">
                    <?php echo htmlspecialchars($article['summary']); ?>
                </p>
            </div>
        </article>
    <?php endforeach; ?>
</div>
