<?php
// Pre-process keywords for display to handle potential JSON legacy data
if (isset($apiConfigs)) {
    foreach ($apiConfigs as &$config) {
        $k = $config['keywords'] ?? '';
        $decoded = json_decode($k, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $config['keywords'] = implode("\n", $decoded);
        }
    }
    unset($config);
}

$title = 'AI 技术发布';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-body-emphasis fw-bold">AI 技术发布</h1>
        <p class="text-muted mb-0">
            <i class="bi bi-lightning-charge me-1"></i> 快速生成并发布 AI 内容
        </p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-sliders me-2"></i>配置选择</h5>
            </div>
            <div class="card-body">
                <form id="publishForm">
                    <div class="mb-3">
                        <label class="form-label fw-bold">选择 API 配置</label>
                        <select class="form-select" id="apiConfigSelect" onchange="loadConfig()">
                            <option value="">请选择...</option>
                            <?php if (isset($apiConfigs)): ?>
                                <?php foreach ($apiConfigs as $config): ?>
                                <option value="<?php echo $config['id']; ?>" 
                                        data-keywords="<?php echo htmlspecialchars($config['keywords'] ?? ''); ?>"
                                        data-region="<?php echo htmlspecialchars($config['geo_region']); ?>"
                                        data-lang="<?php echo htmlspecialchars($config['language']); ?>"
                                        data-promotion-info="<?php echo htmlspecialchars($config['promotion_info'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($config['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">推广植入信息</label>
                        <textarea class="form-control" id="promotionInfo" rows="3" placeholder="此处显示配置的推广信息..."></textarea>
                        <div class="form-text">您可以临时修改此信息用于本次生成。</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">当前关键词池 <span class="text-muted fw-normal">(一行一个)</span></label>
                        <textarea class="form-control font-monospace small bg-light" id="keywordsPool" rows="8" readonly></textarea>
                        <div class="form-text">系统将从中随机抽取 1 个关键词。</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">发布状态</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="publishStatus" id="statusDraft" value="0">
                                <label class="form-check-label" for="statusDraft">
                                    保存为草稿
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="publishStatus" id="statusPublished" value="1" checked>
                                <label class="form-check-label" for="statusPublished">
                                    直接发布
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="button" class="btn btn-primary py-2" id="btnGenerate" onclick="startGeneration()" disabled>
                            <i class="bi bi-robot me-1"></i> 立即生成并发布
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-terminal me-2"></i>生成日志</h5>
                <span class="badge bg-secondary" id="statusBadge">就绪</span>
            </div>
            <div class="card-body bg-dark text-white font-monospace p-0" style="min-height: 500px; max-height: 800px; overflow-y: auto;">
                <div id="consoleLog" class="p-3">
                    <div class="text-white-50">> 等待指令...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function log(message, type = 'info') {
    const consoleDiv = document.getElementById('consoleLog');
    const timestamp = new Date().toLocaleTimeString();
    let color = 'text-white';
    if (type === 'error') color = 'text-danger';
    if (type === 'success') color = 'text-success';
    if (type === 'warning') color = 'text-warning';
    
    consoleDiv.innerHTML += `<div class="${color}">[${timestamp}] ${message}</div>`;
    consoleDiv.scrollTop = consoleDiv.scrollHeight;
}

function loadConfig() {
    const select = document.getElementById('apiConfigSelect');
    const option = select.options[select.selectedIndex];
    const keywords = option.getAttribute('data-keywords');
    const promotionInfo = option.getAttribute('data-promotion-info');
    const btn = document.getElementById('btnGenerate');
    
    document.getElementById('keywordsPool').value = keywords ? keywords : '';
    document.getElementById('promotionInfo').value = promotionInfo ? promotionInfo : '';
    
    if (select.value) {
        btn.disabled = false;
        log(`已加载配置: ${option.text}`);
    } else {
        btn.disabled = true;
        document.getElementById('keywordsPool').value = '';
        document.getElementById('promotionInfo').value = '';
    }
}

function startGeneration() {
    const select = document.getElementById('apiConfigSelect');
    const apiId = select.value;
    if (!apiId) return;

    const keywordsText = document.getElementById('keywordsPool').value;
    const keywords = keywordsText.split('\n').map(k => k.trim()).filter(k => k.length > 0);
    const promotionInfo = document.getElementById('promotionInfo').value;
    const status = document.querySelector('input[name="publishStatus"]:checked').value;

    if (keywords.length === 0) {
        log("错误: 关键词池为空", "error");
        return;
    }

    // Randomly select 1 keyword
    const randomKeyword = keywords[Math.floor(Math.random() * keywords.length)];
    const selectedKeywords = [randomKeyword];

    const btn = document.getElementById('btnGenerate');
    const badge = document.getElementById('statusBadge');
    
    btn.disabled = true;
    badge.className = 'badge bg-warning text-dark';
    badge.innerText = '生成中...';
    
    log("----------------------------------------");
    log(`开始任务...`, "info");
    log(`选中关键词: ${selectedKeywords.join(', ')}`, "info");
    if (promotionInfo) {
        log(`包含推广信息: ${promotionInfo.substring(0, 30)}...`, "info");
    }
    log(`发布模式: ${status == 1 ? '直接发布' : '保存为草稿'}`, "info");
    log(`正在调用 Gemini API (可能需要 15-30 秒)...`, "warning");

    fetch('/admin/api/generate?csrf_token=<?php echo Csrf::generate(); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?php echo Csrf::generate(); ?>'
        },
        body: JSON.stringify({
            api_id: apiId,
            selected_keywords: selectedKeywords,
            promotion_info: promotionInfo,
            article_status: parseInt(status),
            csrf_token: '<?php echo Csrf::generate(); ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            log(`生成成功!`, "success");
            log(`标题: ${data.article.title}`, "success");
            log(`Slug: ${data.article.slug}`, "info");
            log(`已保存到数据库 (ID: ${data.article.id})`, "info");
            badge.className = 'badge bg-success';
            badge.innerText = '完成';
        } else {
            log(`生成失败: ${data.error}`, "error");
            badge.className = 'badge bg-danger';
            badge.innerText = '错误';
        }
    })
    .catch(error => {
        log(`网络错误: ${error.message}`, "error");
        badge.className = 'badge bg-danger';
        badge.innerText = '错误';
    })
    .finally(() => {
        btn.disabled = false;
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
