<?php
$title = isset($article) ? '编辑文章' : '新建文章';
ob_start();

// Fetch categories for dropdown
$categories = Category::getAll();

function buildTree(array $elements, $parentId = 0) {
    $branch = array();
    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = buildTree($elements, $element['id']);
            if ($children) $element['children'] = $children;
            $branch[] = $element;
        }
    }
    return $branch;
}

$categoryTree = buildTree($categories);

function renderCategoryOptions($tree, $selectedId = 0, $level = 0) {
    foreach ($tree as $item) {
        $selected = ($item['id'] == $selectedId) ? 'selected' : '';
        $prefix = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
        if ($level > 0) $prefix .= '└─ ';
        echo '<option value="' . $item['id'] . '" ' . $selected . '>' . $prefix . htmlspecialchars($item['name']) . '</option>';
        if (isset($item['children'])) {
            renderCategoryOptions($item['children'], $selectedId, $level + 1);
        }
    }
}

// Prepare tags if editing
$tagsString = '';
if (isset($article)) {
    $tags = Article::getTags($article['id']);
    $tagNames = array_map(function($t) { return $t['name']; }, $tags);
    $tagsString = implode(', ', $tagNames);
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-gray-800"><?php echo $title; ?></h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/articles" class="btn btn-sm btn-white text-secondary border shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> 返回列表
        </a>
    </div>
</div>

<form method="POST" action="" class="needs-validation" novalidate>
    <?php echo Csrf::input(); ?>
    <div class="row g-4">
        <div class="col-lg-9">
            <!-- Main Content -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="title" class="form-label fw-bold">文章标题</label>
                        <input type="text" class="form-control form-control-lg" id="title" name="title" value="<?php echo htmlspecialchars($article['title'] ?? ''); ?>" required placeholder="请输入文章标题">
                    </div>
                    
                    <div class="mb-4">
                        <label for="slug" class="form-label fw-bold">URL Slug <span class="text-muted fw-normal small">(可选)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">/</span>
                            <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($article['slug'] ?? ''); ?>" placeholder="custom-url-slug">
                        </div>
                        <div class="form-text">留空将根据标题自动生成，仅支持字母、数字和连字符。</div>
                    </div>

                    <div class="mb-4">
                        <label for="content" class="form-label fw-bold">文章内容</label>
                        <!-- UEditor Plus Container -->
                        <script id="editor" name="content" type="text/plain" style="width:100%;height:500px;"><?php echo $article['content'] ?? ''; ?></script>
                        
                        <!-- Initialize UEditor Plus -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var ue = UE.getEditor('editor');
                            });
                        </script>
                    </div>

                    <div class="mb-0">
                        <label for="summary" class="form-label fw-bold">摘要</label>
                        <textarea class="form-control" id="summary" name="summary" rows="3" placeholder="文章摘要，用于列表页展示..."><?php echo htmlspecialchars($article['summary'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-search me-2 text-primary"></i>SEO 设置</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="seo_title" class="form-label">SEO 标题</label>
                        <input type="text" class="form-control" id="seo_title" name="seo_title" value="<?php echo htmlspecialchars($article['seo_title'] ?? ''); ?>" placeholder="如果不填则默认使用文章标题">
                    </div>
                    <div class="mb-3">
                        <label for="seo_keywords" class="form-label">SEO 关键词</label>
                        <input type="text" class="form-control" id="seo_keywords" name="seo_keywords" value="<?php echo htmlspecialchars($article['seo_keywords'] ?? ''); ?>" placeholder="多个关键词用逗号分隔">
                    </div>
                    <div class="mb-0">
                        <label for="seo_description" class="form-label">SEO 描述</label>
                        <textarea class="form-control" id="seo_description" name="seo_description" rows="2" placeholder="如果不填则默认使用摘要"><?php echo htmlspecialchars($article['seo_description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <!-- Sidebar Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-globe me-2 text-primary"></i>发布设置</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">状态</label>
                        <select class="form-select" id="status" name="status">
                            <option value="1" <?php echo (isset($article) && $article['status'] == 1) ? 'selected' : ''; ?>>已发布</option>
                            <option value="0" <?php echo (!isset($article) || $article['status'] == 0) ? 'selected' : ''; ?>>草稿</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="published_at" class="form-label">发布时间</label>
                        <input type="datetime-local" class="form-control" id="published_at" name="published_at" 
                               value="<?php echo isset($article['published_at']) ? date('Y-m-d\TH:i', strtotime($article['published_at'])) : date('Y-m-d\TH:i'); ?>">
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="autoLinkSwitch" name="auto_link" value="1" checked>
                            <label class="form-check-label" for="autoLinkSwitch">自动内链</label>
                        </div>
                        <div class="form-text small">发布时自动根据热门文章标题在正文中添加内链。</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 shadow-sm py-2 fw-bold">
                        <i class="bi bi-save me-1"></i> 保存文章
                    </button>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-tags me-2 text-primary"></i>分类与标签</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">分类</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="0">无分类</option>
                            <?php renderCategoryOptions($categoryTree, $article['category_id'] ?? 0); ?>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label for="tags" class="form-label">标签</label>
                        <input type="text" class="form-control" id="tags" name="tags" value="<?php echo htmlspecialchars($tagsString); ?>" placeholder="标签1, 标签2">
                        <div class="form-text">多个标签用逗号分隔</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-translate me-2 text-primary"></i>区域与语言</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="geo_region" class="form-label">国家/地区</label>
                        <select class="form-select" id="geo_region" name="geo_region">
                            <?php
                            $regions = [
                                'CN' => '中国 (CN)', 
                                'US' => '美国 (US)', 
                                'JP' => '日本 (JP)', 
                                'HK' => '香港 (HK)', 
                                'TW' => '台湾 (TW)', 
                                'GB' => '英国 (GB)', 
                                'SG' => '新加坡 (SG)',
                                'KR' => '韩国 (KR)',
                                'DE' => '德国 (DE)',
                                'FR' => '法国 (FR)'
                            ];
                            $currentGeo = $article['geo_region'] ?? 'CN';
                            foreach ($regions as $code => $name) {
                                $selected = $currentGeo === $code ? 'selected' : '';
                                echo "<option value=\"{$code}\" {$selected}>{$name}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label for="language" class="form-label">语言</label>
                        <select class="form-select" id="language" name="language">
                            <option value="zh-CN" <?php echo (!isset($article) || $article['language'] == 'zh-CN') ? 'selected' : ''; ?>>简体中文</option>
                            <option value="en-US" <?php echo (isset($article) && $article['language'] == 'en-US') ? 'selected' : ''; ?>>English (US)</option>
                            <!-- Add more as needed -->
                        </select>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-image me-2 text-primary"></i>封面图片</h6>
                </div>
                <div class="card-body">
                    <div class="mb-0">
                        <label for="cover_image" class="form-label">图片URL</label>
                        <input type="text" class="form-control" id="cover_image" name="cover_image" value="<?php echo htmlspecialchars($article['cover_image'] ?? ''); ?>" placeholder="https://...">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>