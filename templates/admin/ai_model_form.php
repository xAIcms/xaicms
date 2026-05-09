<?php
$title = ($action === 'create' ? '新建' : '编辑') . ' AI 模型';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1 text-body-emphasis fw-bold"><?php echo $title; ?></h1>
        <p class="text-muted mb-0">
            <i class="bi bi-cpu me-1"></i> 配置 AI 模型接口参数
        </p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/ai-models" class="btn btn-light shadow-sm border">
            <i class="bi bi-arrow-left me-1"></i> 返回列表
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">基本配置</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo Csrf::generate(); ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">配置名称 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($aiModel['name'] ?? ''); ?>" required placeholder="例如：DeepSeek V3, GPT-4o">
                        <div class="form-text">给这个配置起个名字，方便在生成方案中选择。</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">提供商类型</label>
                        <select class="form-select" name="provider">
                            <option value="openai" <?php echo ($aiModel['provider'] ?? '') === 'openai' ? 'selected' : ''; ?>>OpenAI 兼容 (DeepSeek, ChatGPT, etc.)</option>
                            <option value="gemini" <?php echo ($aiModel['provider'] ?? '') === 'gemini' ? 'selected' : ''; ?>>Google Gemini 原生</option>
                        </select>
                        <div class="form-text">如果接口地址包含 /v1/chat/completions，请选择 OpenAI 兼容。</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">API Base URL <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" class="form-control" name="base_url" value="<?php echo htmlspecialchars($aiModel['base_url'] ?? 'https://api.openai.com/v1'); ?>" required>
                        </div>
                        <div class="form-text">
                            例如：<code>https://api.deepseek.com/v1</code> 或 <code>https://generativelanguage.googleapis.com</code>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">API Key <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" name="api_key" value="<?php echo htmlspecialchars($aiModel['api_key'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">模型名称 (Model Name) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="model_name" value="<?php echo htmlspecialchars($aiModel['model_name'] ?? ''); ?>" required placeholder="例如：deepseek-chat, gpt-4o-mini">
                    </div>

                    <div class="mb-3 p-3 bg-light rounded border">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" class="form-check-input" id="isActive" name="is_active" value="1" <?php echo ($aiModel['is_active'] ?? 1) == 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold" for="isActive">启用此模型</label>
                        </div>
                        
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="isLyApi" name="is_ly_api" value="1" <?php echo ($aiModel['is_ly_api'] ?? 0) == 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold" for="isLyApi">洛樱云 API 模式</label>
                            <div class="form-text text-primary small mt-1">开启后将兼容洛樱云 API 规范（POST请求，无 Stream，特殊 Body 结构）。</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <a href="/admin/ai-models" class="btn btn-light border">取消</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> 保存配置
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
            <div class="card-body">
                <h5 class="card-title fw-bold text-primary mb-3"><i class="bi bi-info-circle me-2"></i>配置提示</h5>
                <div class="d-flex flex-column gap-3">
                    <div class="bg-white p-3 rounded shadow-sm">
                        <h6 class="fw-bold mb-2">DeepSeek</h6>
                        <p class="mb-1 small text-muted">Base URL:</p>
                        <code class="d-block mb-2 bg-light p-1 rounded text-break">https://api.deepseek.com</code>
                        <p class="mb-1 small text-muted">Model:</p>
                        <code class="d-block bg-light p-1 rounded">deepseek-chat</code>
                    </div>
                    
                    <div class="bg-white p-3 rounded shadow-sm">
                        <h6 class="fw-bold mb-2">Google Gemini</h6>
                        <p class="mb-1 small text-muted">Base URL:</p>
                        <code class="d-block mb-2 bg-light p-1 rounded text-break">https://generativelanguage.googleapis.com</code>
                        <p class="mb-1 small text-muted">Model:</p>
                        <code class="d-block bg-light p-1 rounded">gemini-2.0-flash-exp</code>
                    </div>

                    <div class="bg-white p-3 rounded shadow-sm">
                        <h6 class="fw-bold mb-2">OpenAI</h6>
                        <p class="mb-1 small text-muted">Base URL:</p>
                        <code class="d-block mb-2 bg-light p-1 rounded text-break">https://api.openai.com/v1</code>
                        <p class="mb-1 small text-muted">Model:</p>
                        <code class="d-block bg-light p-1 rounded">gpt-4o</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>