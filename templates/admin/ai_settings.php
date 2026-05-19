<?php
// templates/admin/ai_settings.php
// AI model settings — configure provider, API key, model

require_once __DIR__ . '/../../src/Models/AiModel.php';
require_once __DIR__ . '/../../src/Models/Settings.php';

$config = require __DIR__ . '/../../config.php';
$aiConfig = $config['ai'] ?? [];

// Handle form submission
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $provider = $_POST['provider'] ?? 'deepseek';
    $apiKey = $_POST['api_key'] ?? '';
    $baseUrl = $_POST['base_url'] ?? 'https://api.deepseek.com/v1';
    $model = $_POST['model'] ?? 'deepseek-v4-flash';
    $proModel = $_POST['pro_model'] ?? 'deepseek-v4-pro';

    // Save to settings table
    try {
        Settings::updateMany([
            'ai_provider' => $provider,
            'ai_api_key' => $apiKey,
            'ai_base_url' => $baseUrl,
            'ai_model' => $model,
            'ai_pro_model' => $proModel,
        ]);

        // Also update/create the default AI model record
        $existingModels = AiModel::getAll();
        $found = false;
        foreach ($existingModels as $m) {
            if ($m['name'] === 'Default') {
                AiModel::update($m['id'], [
                    'provider' => $provider,
                    'api_key' => $apiKey,
                    'base_url' => $baseUrl,
                    'model_name' => $model,
                ]);
                $found = true;
                break;
            }
        }
        if (!$found) {
            AiModel::create([
                'name' => 'Default',
                'provider' => $provider,
                'api_key' => $apiKey,
                'base_url' => $baseUrl,
                'model_name' => $model,
                'is_active' => 1,
            ]);
        }

        $message = 'AI settings saved successfully.';
    } catch (\Exception $e) {
        $error = 'Save failed: ' . $e->getMessage();
    }

    // Refresh config
    $config = require __DIR__ . '/../../config.php';
    $aiConfig = $config['ai'] ?? [];
}

// Read current values (settings take priority over config.php)
$provider = Settings::get('ai_provider', $aiConfig['default_provider'] ?? 'deepseek');
$apiKey = Settings::get('ai_api_key', $aiConfig['default_api_key'] ?? '');
$baseUrl = Settings::get('ai_base_url', $aiConfig['default_base_url'] ?? 'https://api.deepseek.com/v1');
$model = Settings::get('ai_model', $aiConfig['default_model'] ?? 'deepseek-v4-flash');
$proModel = Settings::get('ai_pro_model', $aiConfig['pro_model'] ?? 'deepseek-v4-pro');

$isConfigured = !empty($apiKey);
?>

<div class="container-fluid">
    <h1 class="mb-4">AI Settings</h1>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!$isConfigured): ?>
        <div class="alert alert-warning">
            <strong>AI not configured.</strong> Enter your API key to enable AI content generation.
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <form method="POST">
                <div class="card mb-4">
                    <div class="card-header"><strong>Model Configuration</strong></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Provider</label>
                            <select name="provider" class="form-select">
                                <option value="deepseek" <?php echo $provider === 'deepseek' ? 'selected' : ''; ?>>DeepSeek</option>
                                <option value="openai" <?php echo $provider === 'openai' ? 'selected' : ''; ?>>OpenAI</option>
                                <option value="anthropic" <?php echo $provider === 'anthropic' ? 'selected' : ''; ?>>Anthropic (Claude)</option>
                                <option value="custom" <?php echo $provider === 'custom' ? 'selected' : ''; ?>>Custom (OpenAI-compatible)</option>
                            </select>
                            <div class="form-text">Supports any OpenAI-compatible API endpoint.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">API Key</label>
                            <input type="password" name="api_key" class="form-control" value="<?php echo htmlspecialchars($apiKey); ?>" placeholder="sk-...">
                            <div class="form-text">Your API key stays on your server. Not shared with anyone.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Base URL</label>
                            <input type="text" name="base_url" class="form-control" value="<?php echo htmlspecialchars($baseUrl); ?>" placeholder="https://api.deepseek.com/v1">
                            <div class="form-text">API endpoint. Use <code>https://api.deepseek.com/v1</code> for DeepSeek, <code>https://api.openai.com/v1</code> for OpenAI.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fast Model (Lite)</label>
                                <input type="text" name="model" class="form-control" value="<?php echo htmlspecialchars($model); ?>" placeholder="deepseek-v4-flash">
                                <div class="form-text">Used for quick responses and bulk generation.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Powerful Model (Pro)</label>
                                <input type="text" name="pro_model" class="form-control" value="<?php echo htmlspecialchars($proModel); ?>" placeholder="deepseek-v4-pro">
                                <div class="form-text">Used for complex content and deep analysis.</div>
                            </div>
                        </div>

                        <button type="submit" name="save" value="1" class="btn btn-primary">Save Settings</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><strong>Getting an API Key</strong></div>
                <div class="card-body">
                    <p class="small">Choose a provider:</p>
                    <ul class="small">
                        <li><a href="https://platform.deepseek.com/api_keys" target="_blank">DeepSeek</a> — cheapest, great for bulk content</li>
                        <li><a href="https://platform.openai.com/api-keys" target="_blank">OpenAI</a> — most powerful, higher cost</li>
                    </ul>
                </div>
            </div>

            <div class="card bg-light">
                <div class="card-body">
                    <h6>Coming Soon</h6>
                    <p class="small text-muted mb-0">
                        <strong>xAIcms Relay</strong> — No API key needed.
                        We handle the AI backend so you don't have to.
                        Simple monthly pricing. Stay tuned.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
