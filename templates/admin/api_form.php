<?php
// templates/admin/api_form.php
require_once __DIR__ . '/../../src/Config/AppConfig.php';

$title = $isEdit ? '编辑 API' : '新建 API';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-body-emphasis fw-bold"><?php echo $title; ?></h1>
        <p class="text-muted mb-0">
            <i class="bi bi-globe me-1"></i> 管理对外发布的 API 接口配置
        </p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/api" class="btn btn-light shadow-sm border">
            <i class="bi bi-arrow-left me-1"></i> 返回列表
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-danger-subtle" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">基本信息</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="" class="needs-validation" novalidate>
                    <?php echo Csrf::input(); ?>
                    
                    <?php if ($isEdit && !empty($api['scheme_id']) && !empty($api['user_id'])): ?>
                        <?php 
                        require_once __DIR__ . '/../../src/Models/User.php';
                        $schemeUser = User::findById($api['user_id']);
                        if ($schemeUser): 
                        ?>
                        <div class="alert alert-indigo mb-4 d-flex align-items-center bg-indigo-50 text-indigo-700 border-indigo-200">
                            <i class="bi bi-person-circle fs-4 me-3"></i>
                            <div>
                                <div class="fw-bold">用户提交方案</div>
                                <div class="small">提交用户: <?php echo htmlspecialchars($schemeUser['name']); ?> (ID: <?php echo $schemeUser['id']; ?>) | 积分余额: <?php echo $schemeUser['points']; ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($isEdit): ?>
                    <div class="mb-4">
                        <label class="form-label fw-bold">API Key</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                            <input type="text" class="form-control font-monospace text-danger bg-light" value="<?php echo htmlspecialchars($api['api_key']); ?>" readonly id="apiKeyField">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyApiKey()">
                                <i class="bi bi-clipboard me-1"></i> 复制
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="form-label fw-bold">配置名称</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($api['name'] ?? ''); ?>" required placeholder="例如: 中国区推广接口">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">AI 模型</label>
                        <select name="ai_model_id" class="form-select">
                            <option value="0">使用系统默认设置</option>
                            <?php if (!empty($aiModels)): ?>
                                <?php foreach ($aiModels as $model): ?>
                                    <option value="<?php echo $model['id']; ?>" <?php echo (isset($api['ai_model_id']) && $api['ai_model_id'] == $model['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($model['name']); ?> (<?php echo htmlspecialchars($model['model_name']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="form-text">选择用于生成文章的 AI 模型。如不选择，将使用系统设置中的默认 Gemini API。</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">目标地区 (ISO Code)</label>
                            <select name="geo_region" class="form-select">
                                <?php
                                $regions = AppConfig::REGIONS;
                                $currentGeo = $api['geo_region'] ?? 'CN';
                                // Check if current value is not in list (legacy support)
                                if (!array_key_exists($currentGeo, $regions) && !empty($currentGeo)) {
                                    $regions[$currentGeo] = $currentGeo . ' (Custom)';
                                }
                                
                                foreach ($regions as $code => $name) {
                                    $selected = $currentGeo === $code ? 'selected' : '';
                                    echo "<option value=\"{$code}\" {$selected}>{$name}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">语言代码</label>
                            <select name="language" class="form-select">
                                <?php
                                $languages = AppConfig::LANGUAGES;
                                $currentLang = $api['language'] ?? 'zh-CN';
                                // Support custom values not in list
                                if (!array_key_exists($currentLang, $languages) && !empty($currentLang)) {
                                    $languages[$currentLang] = $currentLang . ' (Custom)';
                                }
                                
                                foreach ($languages as $code => $name) {
                                    $selected = $currentLang === $code ? 'selected' : '';
                                    echo "<option value=\"{$code}\" {$selected}>{$name}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">默认分类</label>
                        <select name="category_id" class="form-select">
                            <option value="0">无</option>
                            <?php 
                            if (isset($categories)) {
                                foreach ($categories as $cat) {
                                    $selected = ($api['category_id'] ?? 0) == $cat['id'] ? 'selected' : '';
                                    echo '<option value="' . $cat['id'] . '" ' . $selected . '>' . htmlspecialchars($cat['name']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <h5 class="card-title fw-bold mt-5 mb-3 pt-3 border-top">生成策略</h5>

                    <div class="mb-4">
                        <label class="form-label fw-bold">推广关键词 (一行一个)</label>
                        <?php
                        // Handle potential JSON legacy data
                        $keywordsValue = $api['keywords'] ?? '';
                        $decodedKeywords = json_decode($keywordsValue, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedKeywords)) {
                            $keywordsValue = implode("\n", $decodedKeywords);
                        }
                        ?>
                        <textarea name="keywords" class="form-control" rows="6" placeholder="keyword1&#10;keyword2&#10;keyword3"><?php echo htmlspecialchars($keywordsValue); ?></textarea>
                        <div class="form-text">每行输入一个关键词，系统将随机抽取用于生成文章。</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">单次生成关键词数量</label>
                        <input type="number" name="keyword_count" class="form-control" value="<?php echo htmlspecialchars($api['keyword_count'] ?? 1); ?>" min="1" max="20" required>
                        <div class="form-text">每次生成文章时，从上述关键词池中随机抽取的关键词数量。</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">推广植入信息 (与提示词结合)</label>
                        <textarea name="promotion_info" class="form-control" rows="4" placeholder="输入需要 AI 植入的产品或品牌信息..."><?php echo htmlspecialchars($api['promotion_info'] ?? ''); ?></textarea>
                        <div class="form-text">此信息将作为 Prompt 的一部分发送给 AI，请使用自然语言描述。</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">每日发布限制 (0为不限制)</label>
                        <input type="number" name="daily_limit" class="form-control" value="<?php echo htmlspecialchars($api['daily_limit'] ?? 0); ?>" min="0">
                        <div class="form-text">控制每天生成的文章数量，避免短时间大量发布。</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">生成文章状态</label>
                            <select name="article_status" class="form-select">
                                <option value="1" <?php echo ($api['article_status'] ?? 1) == 1 ? 'selected' : ''; ?>>直接发布 (Published)</option>
                                <option value="0" <?php echo ($api['article_status'] ?? 1) == 0 ? 'selected' : ''; ?>>存为草稿 (Draft)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">插入图片数量</label>
                            <input type="number" name="insert_image_count" class="form-control" value="<?php echo htmlspecialchars($api['insert_image_count'] ?? 0); ?>" min="0" max="10">
                            <div class="form-text">0 表示不插入图片</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">图片插入位置</label>
                            <select name="insert_image_position" class="form-select">
                                <option value="random" <?php echo ($api['insert_image_position'] ?? 'random') == 'random' ? 'selected' : ''; ?>>随机位置</option>
                                <option value="average" <?php echo ($api['insert_image_position'] ?? '') == 'average' ? 'selected' : ''; ?>>平均分布</option>
                                <option value="head" <?php echo ($api['insert_image_position'] ?? '') == 'head' ? 'selected' : ''; ?>>文章开头</option>
                                <option value="tail" <?php echo ($api['insert_image_position'] ?? '') == 'tail' ? 'selected' : ''; ?>>文章结尾</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">图片来源</label>
                            <select name="image_source_type" class="form-select" id="imageSourceSelect">
                                <option value="search" <?php echo ($api['image_source_type'] ?? 'search') == 'search' ? 'selected' : ''; ?>>Google 图片搜索</option>
                                <option value="local_random" <?php echo ($api['image_source_type'] ?? '') == 'local_random' ? 'selected' : ''; ?>>本地图库 (随机)</option>
                                <option value="local_match" <?php echo ($api['image_source_type'] ?? '') == 'local_match' ? 'selected' : ''; ?>>本地图库 (文件名匹配)</option>
                                <option value="custom_url" <?php echo ($api['image_source_type'] ?? '') == 'custom_url' ? 'selected' : ''; ?>>自定义图片 URL</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4" id="customUrlArea" style="display: none;">
                        <label class="form-label fw-bold">自定义图片 URL (一行一个)</label>
                        <?php
                        // Handle custom URLs array
                        $customUrlsValue = $api['custom_image_urls'] ?? '';
                        $decodedUrls = json_decode($customUrlsValue, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedUrls)) {
                            $customUrlsValue = implode("\n", $decodedUrls);
                        }
                        ?>
                        <textarea name="custom_image_urls" class="form-control" rows="4" placeholder="https://example.com/image1.jpg&#10;https://example.com/image2.jpg"><?php echo htmlspecialchars($customUrlsValue); ?></textarea>
                        <div class="form-text">系统将从这些 URL 中随机选择图片插入文章。</div>
                    </div>
                    
                    <div class="mb-4" id="mediaCategoryArea" style="display: none;">
                        <label class="form-label fw-bold">本地媒体分类</label>
                        <select name="media_category_id" class="form-select">
                            <option value="0">所有图片</option>
                            <?php 
                            if (isset($mediaCategories)) {
                                foreach ($mediaCategories as $cat) {
                                    $selected = ($api['media_category_id'] ?? 0) == $cat['id'] ? 'selected' : '';
                                    echo '<option value="' . $cat['id'] . '" ' . $selected . '>' . htmlspecialchars($cat['name']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <div class="form-text">选择要使用的本地图片分类。</div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="autoLink" name="auto_link" value="1" <?php echo ($api['auto_link'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold" for="autoLink">启用自动内链</label>
                        </div>
                        <div class="form-text">开启后，系统会自动将文章中的关键词链接到站内相关文章（基于标题匹配）。</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">状态</label>
                        <select name="status" class="form-select">
                            <option value="1" <?php echo ($api['status'] ?? 1) == 1 ? 'selected' : ''; ?>>启用 (Active)</option>
                            <option value="0" <?php echo ($api['status'] ?? 1) == 0 ? 'selected' : ''; ?>>禁用 (Disabled)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-5">
                        <a href="/admin/api" class="btn btn-light border">取消</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> 保存配置
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">API 使用说明</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">使用此 API 密钥调用接口生成文章。请妥善保管您的密钥。</p>
                <hr>
                <h6 class="fw-bold text-dark">请求示例</h6>
                <div class="bg-light p-3 rounded mb-3 position-relative">
                    <button class="btn btn-sm btn-outline-secondary position-absolute top-0 end-0 m-2" onclick="copyCurl()">
                        <i class="bi bi-clipboard"></i>
                    </button>
                    <code class="d-block text-break small" id="curlExample">
curl -X POST <?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>/api/generate \
  -H "Authorization: Bearer <span class="text-danger"><?php echo $isEdit ? htmlspecialchars($api['api_key']) : 'YOUR_API_KEY'; ?></span>" \
  -H "Content-Type: application/json" \
  -d '{
    "topic": "人工智能的发展趋势"
  }'
                    </code>
                </div>
                
                <h6 class="fw-bold text-dark mt-4">参数说明</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2">
                        <code>topic</code> <span class="badge bg-secondary">可选</span>
                        <div class="ms-2">指定文章主题。如不提供，将从关键词列表中随机选择。</div>
                    </li>
                </ul>
            </div>
        </div>
        
        <?php if ($isEdit && !empty($api['user_id'])): ?>
        <div class="card border-0 shadow-sm mb-4 bg-primary-subtle">
            <div class="card-header bg-transparent py-3">
                <h5 class="card-title mb-0 fw-bold text-primary">
                    <i class="bi bi-person-badge me-2"></i>用户方案信息
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.2rem;">
                            <?php echo strtoupper(substr($api['user_name'] ?? 'U', 0, 1)); ?>
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($api['user_name'] ?? 'Unknown User'); ?></h6>
                        <small class="text-muted">积分: <?php echo $api['user_points'] ?? 0; ?> | 冻结: <?php echo $api['user_frozen_points'] ?? 0; ?></small>
                    </div>
                </div>
                
                <hr class="border-primary-subtle">
                
                <p class="mb-2"><small class="text-muted">方案 ID:</small> <strong>#<?php echo $api['scheme_id']; ?></strong></p>
                
                <?php if(isset($api['scheme_status'])): ?>
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">方案状态:</small>
                    <?php
                    $statusClass = '';
                    $statusText = '';
                    switch($api['scheme_status']) {
                        case 'pending': $statusClass = 'bg-warning text-dark'; $statusText = '待审核'; break;
                        case 'approved': $statusClass = 'bg-info text-dark'; $statusText = '已审核'; break;
                        case 'running': $statusClass = 'bg-success text-white'; $statusText = '运行中'; break;
                        case 'paused': $statusClass = 'bg-secondary text-white'; $statusText = '已暂停'; break;
                        case 'completed': $statusClass = 'bg-primary text-white'; $statusText = '已完成'; break;
                        case 'rejected': $statusClass = 'bg-danger text-white'; $statusText = '已拒绝'; break;
                    }
                    ?>
                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                </div>
                
                <form action="/admin/api/approve-scheme" method="POST" class="d-grid gap-2">
                    <input type="hidden" name="api_id" value="<?php echo $api['id']; ?>">
                    <input type="hidden" name="scheme_id" value="<?php echo $api['scheme_id']; ?>">
                    
                    <?php if($api['scheme_status'] === 'pending'): ?>
                        <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">
                            <i class="bi bi-check-circle me-1"></i> 通过审核并激活
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-1"></i> 拒绝方案
                        </button>
                    <?php elseif($api['scheme_status'] === 'running' || $api['scheme_status'] === 'approved'): ?>
                        <button type="submit" name="action" value="pause" class="btn btn-warning btn-sm">
                            <i class="bi bi-pause-circle me-1"></i> 暂停方案
                        </button>
                    <?php elseif($api['scheme_status'] === 'paused'): ?>
                        <button type="submit" name="action" value="resume" class="btn btn-success btn-sm">
                            <i class="bi bi-play-circle me-1"></i> 恢复运行
                        </button>
                    <?php endif; ?>
                </form>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="/admin/api/approve-scheme" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">拒绝方案</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="api_id" value="<?php echo $api['id']; ?>">
                            <input type="hidden" name="scheme_id" value="<?php echo $api['scheme_id']; ?>">
                            <input type="hidden" name="action" value="reject">
                            <div class="mb-3">
                                <label for="rejectReason" class="form-label">拒绝理由</label>
                                <textarea class="form-control" id="rejectReason" name="reason" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                            <button type="submit" class="btn btn-danger">确认拒绝</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function copyApiKey() {
        var copyText = document.getElementById("apiKeyField");
        copyText.select();
        copyText.setSelectionRange(0, 99999); 
        navigator.clipboard.writeText(copyText.value);
        
        // Show tooltip or toast could be added here
        alert("API Key 已复制到剪贴板");
    }
    
    function copyCurl() {
        var copyText = document.getElementById("curlExample").innerText;
        navigator.clipboard.writeText(copyText);
        alert("CURL 命令已复制");
    }
    
    // Toggle image source options
    document.getElementById('imageSourceSelect').addEventListener('change', function() {
        const val = this.value;
        const customUrlArea = document.getElementById('customUrlArea');
        const mediaCategoryArea = document.getElementById('mediaCategoryArea');
        
        if (val === 'custom_url') {
            customUrlArea.style.display = 'block';
            mediaCategoryArea.style.display = 'none';
        } else if (val === 'local_random' || val === 'local_match') {
            customUrlArea.style.display = 'none';
            mediaCategoryArea.style.display = 'block';
        } else {
            customUrlArea.style.display = 'none';
            mediaCategoryArea.style.display = 'none';
        }
    });
    
    // Trigger change on load to set initial state
    document.getElementById('imageSourceSelect').dispatchEvent(new Event('change'));
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
