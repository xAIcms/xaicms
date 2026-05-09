<?php
$title = 'API 管理';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 fw-bold text-gray-800">API 密钥管理</h1>
        <p class="text-muted small mb-0">管理您的 API 密钥和 AI 内容生成配置</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/api/publish" class="btn btn-sm btn-success me-2 shadow-sm">
            <i class="bi bi-robot me-1"></i> AI 技术发布
        </a>
        <a href="/admin/api/create" class="btn btn-sm btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> 新建 API 密钥
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3 border-bottom-0">名称 / 用户</th>
                        <th class="py-3 border-bottom-0">配置信息</th>
                        <th class="py-3 border-bottom-0">API Key</th>
                        <th class="py-3 border-bottom-0">进度 / 调用</th>
                        <th class="py-3 border-bottom-0">状态</th>
                        <th class="pe-4 py-3 border-bottom-0 text-end">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($apis)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div class="py-4">
                                <i class="bi bi-key fs-1 d-block mb-3 opacity-50"></i>
                                <p class="mb-0">暂无 API 配置</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($apis as $api): ?>
                        <tr class="<?php echo !empty($api['scheme_id']) ? 'bg-indigo-50 bg-opacity-10' : ''; ?>">
                            <td class="ps-4 py-3">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($api['name']); ?></div>
                                <?php if (!empty($api['user_name'])): ?>
                                    <div class="small text-primary mt-1">
                                        <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($api['user_name']); ?>
                                        <span class="text-muted ms-1">(余额: <?php echo $api['user_points']; ?>)</span>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3">
                                <div class="small text-muted mb-1">
                                    <i class="bi bi-globe2 me-1"></i> <?php echo htmlspecialchars($api['geo_region']); ?> 
                                    <span class="mx-1">/</span> 
                                    <i class="bi bi-translate me-1"></i> <?php echo htmlspecialchars($api['language']); ?>
                                </div>
                                <?php if (!empty($api['ai_model_name'])): ?>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2">
                                        <i class="bi bi-cpu me-1"></i><?php echo htmlspecialchars($api['ai_model_name']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2">系统默认</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center">
                                    <code class="bg-light px-2 py-1 rounded text-danger me-2 border small font-monospace" title="<?php echo htmlspecialchars($api['api_key']); ?>">
                                        <?php echo substr($api['api_key'], 0, 8) . '•••' . substr($api['api_key'], -8); ?>
                                    </code>
                                    <button class="btn btn-icon btn-sm btn-light rounded-circle text-muted" onclick="copyToClipboard('<?php echo $api['api_key']; ?>')" title="复制 API Key" data-bs-toggle="tooltip">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="py-3">
                                <?php if (!empty($api['scheme_id'])): ?>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium font-monospace text-dark">
                                            <?php echo $api['generated_count']; ?> / <?php echo $api['target_count']; ?>
                                        </span>
                                        <span class="small text-muted">
                                            冻结: <?php echo $api['frozen_points']; ?>
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <span class="fw-medium font-monospace"><?php echo number_format($api['call_count']); ?></span>
                                <?php endif; ?>

                                <?php if (!empty($api['daily_limit']) && $api['daily_limit'] > 0): ?>
                                    <div class="small text-warning mt-1" title="每日发布限制">
                                        <i class="bi bi-speedometer2 me-1"></i><?php echo $api['daily_limit']; ?> / 天
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3">
                                <?php if (!empty($api['scheme_id']) && $api['scheme_status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-2">待审核</span>
                                <?php elseif (!empty($api['scheme_id']) && $api['scheme_status'] === 'rejected'): ?>
                                    <span class="badge bg-danger rounded-pill px-2">已拒绝</span>
                                <?php elseif (!empty($api['scheme_id']) && $api['scheme_status'] === 'completed'): ?>
                                    <span class="badge bg-success rounded-pill px-2">已完成</span>
                                <?php else: ?>
                                    <?php if ($api['status'] == 1): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">
                                            <span class="d-inline-block rounded-circle bg-success me-1" style="width: 6px; height: 6px;"></span>
                                            启用
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2">
                                            <span class="d-inline-block rounded-circle bg-secondary me-1" style="width: 6px; height: 6px;"></span>
                                            禁用
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <div class="btn-group">
                                    <?php if (!empty($api['scheme_id']) && $api['scheme_status'] === 'pending'): ?>
                                        <a href="/admin/ai-schemes/approve/<?php echo $api['scheme_id']; ?>" class="btn btn-sm btn-success shadow-sm" title="通过">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                        <a href="/admin/ai-schemes/reject/<?php echo $api['scheme_id']; ?>" class="btn btn-sm btn-danger shadow-sm" title="拒绝" onclick="return confirm('确定要拒绝该方案吗？将全额退还冻结积分。')">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                    <?php endif; ?>

                                    <a href="/admin/api/edit/<?php echo $api['id']; ?>" class="btn btn-sm btn-white border text-primary shadow-sm" title="编辑">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <?php if (empty($api['scheme_id']) || $api['scheme_status'] !== 'pending'): ?>
                                        <button type="button" class="btn btn-sm btn-white border text-danger shadow-sm" onclick="deleteApi(<?php echo $api['id']; ?>)" title="删除">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-3 px-4">
        <h5 class="mb-0 card-title h6 fw-bold text-dark"><i class="bi bi-code-square me-2 text-primary"></i>API 调用说明</h5>
    </div>
    <div class="card-body p-4">
        <p class="text-muted mb-4">使用 API Key 可以通过 RESTful API 访问内容数据。以下是调用示例：</p>
        
        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-2">1. 获取文章列表</h6>
            <div class="position-relative">
                <button class="btn btn-sm btn-dark position-absolute top-0 end-0 m-2 opacity-75" onclick="copyCode(this)">
                    <i class="bi bi-clipboard me-1"></i> 复制
                </button>
                <pre class="bg-dark text-light p-3 rounded-3 m-0 shadow-inner code-block"><code>GET /api/articles
Headers:
  X-API-Key: your_api_key_here
  Accept: application/json</code></pre>
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-2">2. 获取单篇文章</h6>
            <div class="position-relative">
                <button class="btn btn-sm btn-dark position-absolute top-0 end-0 m-2 opacity-75" onclick="copyCode(this)">
                    <i class="bi bi-clipboard me-1"></i> 复制
                </button>
                <pre class="bg-dark text-light p-3 rounded-3 m-0 shadow-inner code-block"><code>GET /api/articles/{uuid}
Headers:
  X-API-Key: your_api_key_here
  Accept: application/json</code></pre>
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-2">3. 生成文章 (AI Generation)</h6>
            <div class="position-relative">
                <button class="btn btn-sm btn-dark position-absolute top-0 end-0 m-2 opacity-75" onclick="copyCode(this)">
                    <i class="bi bi-clipboard me-1"></i> 复制
                </button>
                <pre class="bg-dark text-light p-3 rounded-3 m-0 shadow-inner code-block"><code>POST /api/generate
Headers:
  X-API-Key: your_api_key_here
  Accept: application/json

Note: 
此接口不需要 Body 参数。
系统会自动从 API 方案中配置的关键词池中随机选择一个关键词，
并结合配置的推广信息生成文章并自动发布。</code></pre>
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-2">4. cURL 示例</h6>
            <p class="text-muted small mb-2">获取文章列表：</p>
            <div class="position-relative mb-3">
                <button class="btn btn-sm btn-dark position-absolute top-0 end-0 m-2 opacity-75" onclick="copyCode(this)">
                    <i class="bi bi-clipboard me-1"></i> 复制
                </button>
                <pre class="bg-dark text-light p-3 rounded-3 m-0 shadow-inner code-block"><code>curl -X GET "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>/api/articles" \
     -H "X-API-Key: your_api_key_here" \
     -H "Accept: application/json"</code></pre>
            </div>

            <p class="text-muted small mb-2">一键生成新文章：</p>
            <div class="position-relative">
                <button class="btn btn-sm btn-dark position-absolute top-0 end-0 m-2 opacity-75" onclick="copyCode(this)">
                    <i class="bi bi-clipboard me-1"></i> 复制
                </button>
                <pre class="bg-dark text-light p-3 rounded-3 m-0 shadow-inner code-block"><code>curl -X POST "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>/api/generate" \
     -H "X-API-Key: your_api_key_here" \
     -H "Content-Length: 0"</code></pre>
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-2">5. 宝塔定时计划设置 (小白教程)</h6>
            <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info">
                <i class="bi bi-info-circle-fill me-2"></i>
                <strong>为什么需要设置定时任务？</strong><br>
                设置定时任务后，服务器会按照您设定的时间间隔（如每天或每小时）自动调用 API 生成新文章，无需人工干预即可保持网站内容的持续更新。
            </div>
            
            <p class="text-muted small mb-2">请复制以下脚本代码：</p>
            <div class="position-relative mb-3">
                <button class="btn btn-sm btn-dark position-absolute top-0 end-0 m-2 opacity-75" onclick="copyCode(this)">
                    <i class="bi bi-clipboard me-1"></i> 复制
                </button>
                <pre class="bg-dark text-light p-3 rounded-3 m-0 shadow-inner code-block"><code># 设置 API Key (请替换为您的真实 Key)
API_KEY="your_api_key_here"

# 网站域名 (请替换为您网站的实际域名，如 http://example.com)
SITE_URL="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>"

# 调用 API 生成文章
curl -X POST "${SITE_URL}/api/generate" \
     -H "X-API-Key: ${API_KEY}" \
     -H "Content-Length: 0"

# 输出执行时间
echo "Task executed at $(date)"</code></pre>
            </div>

            <p class="text-muted small mb-2">或者使用更简单的 <strong>访问 URL (GET/POST)</strong> 方式：</p>
            <div class="alert alert-light border small text-muted mb-3">
                <i class="bi bi-lightbulb me-1"></i> 提示：这种方式适用于任务类型选择 <strong>“访问URL”</strong> 的情况，更加简单直接。
            </div>
            <div class="position-relative mb-3">
                <button class="btn btn-sm btn-dark position-absolute top-0 end-0 m-2 opacity-75" onclick="copyCode(this)">
                    <i class="bi bi-clipboard me-1"></i> 复制
                </button>
                <pre class="bg-dark text-light p-3 rounded-3 m-0 shadow-inner code-block"><code><?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>/api/generate?api_key=your_api_key_here</code></pre>
            </div>

            <h6 class="text-dark fw-bold small mt-3 mb-2">设置步骤：</h6>
            <ol class="small text-muted ps-3 mb-0">
                <li class="mb-1">登录 <strong>宝塔面板</strong>，点击左侧菜单的 <strong>“计划任务”</strong>。</li>
                <li class="mb-1">任务类型选择 <strong>“Shell脚本”</strong>。</li>
                <li class="mb-1">任务名称填写 <strong>“自动发布文章”</strong>。</li>
                <li class="mb-1">执行周期建议选择 <strong>“每天”</strong> 或 <strong>“N小时”</strong> (建议间隔大于 1 小时)。</li>
                <li class="mb-1">在脚本内容框中，粘贴上方复制代码，并<strong>务必修改</strong> <code>API_KEY</code> 为您在上方列表中获取的实际 Key。</li>
                <li class="mb-1">点击 <strong>“添加任务”</strong> 即可。</li>
            </ol>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
  <div id="copyToast" class="toast align-items-center text-white bg-success border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <i class="bi bi-check-circle-fill me-2"></i> <span id="toastMessage">内容已复制到剪贴板！</span>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>



<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

function deleteApi(id) {
    if (confirm('确定要删除这个API配置吗？')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/api/delete/' + id;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = 'csrf_token';
        csrf.value = '<?php echo Csrf::generate(); ?>';
        
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}

function showToast(message) {
    const toastEl = document.getElementById('copyToast');
    const toastBody = document.getElementById('toastMessage');
    toastBody.textContent = message || '内容已复制到剪贴板！';
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('API Key 已复制！');
    }, function(err) {
        // Fallback for older browsers
        var textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            var successful = document.execCommand('copy');
            if(successful) {
                showToast('API Key 已复制！');
            } else {
                alert('复制失败，请手动复制');
            }
        } catch (err) {
            alert('复制失败，请手动复制');
        }
        document.body.removeChild(textArea);
    });
}

function copyCode(btn) {
    var codeBlock = btn.nextElementSibling;
    var code = codeBlock.innerText;
    
    navigator.clipboard.writeText(code).then(function() {
        showToast('代码已复制！');
        
        // Button feedback
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> 已复制';
        btn.classList.remove('btn-dark');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.add('btn-dark');
            btn.classList.remove('btn-success');
        }, 2000);
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
