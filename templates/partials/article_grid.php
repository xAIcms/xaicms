<div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
    <?php foreach ($articles as $article): ?>
        <a href="/<?php echo htmlspecialchars($article['slug']); ?>.html" class="group h-full flex flex-col no-underline">
            <article class="h-full flex flex-col">
                <div class="relative aspect-[16/10] overflow-hidden rounded-2xl mb-5 bg-slate-100 shadow-sm border border-slate-100/50">
                    <img src="<?php echo htmlspecialchars(!empty($article['cover_image']) ? $article['cover_image'] : 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&q=80&w=800'); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="absolute inset-0 w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                    
                </div>

                <div class="flex-1 flex flex-col">
                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400 mb-3 font-medium">
                        <span class="text-indigo-600">
                            <?php echo htmlspecialchars($article['category_name'] ?? 'Article'); ?>
                        </span>
                        <span>•</span>
                        <span><?php echo date('M d, Y', strtotime($article['published_at'])); ?></span>
                        
                        <?php 
                        $tags = Article::getTags($article['id']);
                        if (!empty($tags)):
                            echo '<span>•</span>';
                            foreach (array_slice($tags, 0, 2) as $tag):
                        ?>
                            <a href="/tag/<?php echo htmlspecialchars($tag['slug']); ?>.html" class="hover:text-indigo-600 transition-colors">#<?php echo htmlspecialchars($tag['name']); ?></a>
                        <?php 
                            endforeach;
                        endif; 
                        ?>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 mb-3 group-hover:text-indigo-600 transition-colors leading-tight">
                        <?php echo htmlspecialchars($article['title']); ?>
                    </h3>

                    <p class="text-slate-500 text-sm line-clamp-3 mb-4 leading-relaxed flex-1">
                        <?php echo htmlspecialchars($article['summary']); ?>
                    </p>

                    <div class="flex items-center text-indigo-600 text-sm font-bold mt-auto group-hover:translate-x-1 transition-transform">
                        Read Article <i class="bi bi-arrow-right ml-1"></i>
                    </div>
                </div>
            </article>
        </a>
    <?php endforeach; ?>
</div>
