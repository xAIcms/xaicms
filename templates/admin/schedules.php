<?php
// templates/admin/schedules.php
// Task scheduler management — create, edit, delete scheduled tasks

require_once __DIR__ . '/../../src/Core/Scheduler.php';

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $taskName = $_POST['task_name'] ?? 'publish_article';
        $interval = (int)($_POST['interval_seconds'] ?? 86400);
        $desc = $_POST['description'] ?? '';
        $config = [];

        if ($taskName === 'publish_article') {
            $config['article_id'] = (int)($_POST['article_id'] ?? 0);
        } elseif ($taskName === 'generate_article') {
            $config['model_id'] = (int)($_POST['model_id'] ?? 0);
            $config['category_id'] = (int)($_POST['category_id'] ?? 0);
            $config['language'] = $_POST['language'] ?? 'zh-CN';
            $config['region'] = $_POST['region'] ?? 'CN';
            $config['topic'] = $_POST['topic'] ?? '';
            $config['prompt'] = $_POST['prompt'] ?? '';
        } else {
            $config['data'] = $_POST['config_json'] ?? '{}';
            $config = @json_decode($config['data'], true) ?: [];
        }

        Scheduler::create($taskName, $config, $interval, $desc);
        $message = 'Schedule created.';
    }

    if (isset($_POST['delete_id'])) {
        Scheduler::delete((int)$_POST['delete_id']);
        $message = 'Schedule deleted.';
    }

    if (isset($_POST['toggle_id'])) {
        Scheduler::toggle((int)$_POST['toggle_id']);
        $message = 'Schedule toggled.';
    }
}

$schedules = Scheduler::getAll();

// Helpers for display
function intervalLabel(int $seconds): string {
    $map = [
        3600 => 'Hourly',
        21600 => 'Every 6 hours',
        43200 => 'Every 12 hours',
        86400 => 'Daily',
        604800 => 'Weekly',
        2592000 => 'Monthly',
    ];
    return $map[$seconds] ?? "Every $seconds seconds";
}

function taskIcon(string $name): string {
    return match($name) {
        'publish_article' => '📅',
        'generate_article' => '🤖',
        default => '⚙',
    };
}

// Get AI models for dropdown
$aiModels = [];
if (class_exists('AiModel')) {
    $aiModels = AiModel::getAll();
}

// Get articles for dropdown
$articles = [];
if (class_exists('Article')) {
    $articles = Article::getAll(200, 0);
}
?>

<div class="container-fluid">
    <h1 class="mb-4">Task Scheduler</h1>

    <?php if ($message): ?><div class="alert alert-success"><?php echo $message; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

    <div class="alert alert-info">
        <strong>No cron job needed.</strong> Scheduled tasks run automatically when someone visits your site — just like WordPress.
    </div>

    <!-- Existing tasks -->
    <div class="card mb-4">
        <div class="card-header"><strong>Active Schedules</strong></div>
        <div class="card-body p-0">
            <?php if (empty($schedules)): ?>
                <div class="text-center py-4 text-muted">No scheduled tasks yet.</div>
            <?php else: ?>
                <table class="table mb-0">
                    <thead><tr>
                        <th>Task</th><th>Description</th><th>Interval</th><th>Next Run</th><th>Last Run</th><th>Actions</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($schedules as $s): ?>
                        <tr class="<?php echo $s['enabled'] ? '' : 'text-muted'; ?>">
                            <td><?php echo taskIcon($s['task_name']) . ' ' . htmlspecialchars($s['task_name']); ?></td>
                            <td class="small"><?php echo htmlspecialchars($s['description']); ?></td>
                            <td><?php echo intervalLabel($s['interval_seconds']); ?></td>
                            <td class="small"><?php echo $s['next_run_at']; ?></td>
                            <td class="small"><?php echo $s['last_run_at'] ?? 'Never'; ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="toggle_id" value="<?php echo $s['id']; ?>">
                                    <button class="btn btn-sm <?php echo $s['enabled'] ? 'btn-outline-warning' : 'btn-outline-success'; ?>">
                                        <?php echo $s['enabled'] ? 'Pause' : 'Resume'; ?>
                                    </button>
                                </form>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                    <input type="hidden" name="delete_id" value="<?php echo $s['id']; ?>">
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create new schedule -->
    <div class="card">
        <div class="card-header"><strong>Create Schedule</strong></div>
        <div class="card-body">
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Task Type</label>
                        <select name="task_name" class="form-select" id="task-type">
                            <option value="publish_article">Publish Article</option>
                            <option value="generate_article">Generate Article (AI)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Interval</label>
                        <select name="interval_seconds" class="form-select">
                            <option value="3600">Hourly</option>
                            <option value="43200">Every 12 hours</option>
                            <option value="86400" selected>Daily</option>
                            <option value="604800">Weekly</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" placeholder="e.g. Daily AI post">
                    </div>
                </div>

                <!-- Publish Article fields -->
                <div id="publish-fields" class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Article to Publish</label>
                        <select name="article_id" class="form-select">
                            <option value="">-- Select draft --</option>
                            <?php foreach ($articles as $a): ?>
                                <?php if ($a['status'] == 0): ?>
                                    <option value="<?php echo $a['id']; ?>">#<?php echo $a['id']; ?> <?php echo htmlspecialchars($a['title'] ?? ''); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Generate Article fields -->
                <div id="generate-fields" class="d-none">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">AI Model</label>
                            <select name="model_id" class="form-select">
                                <?php foreach ($aiModels as $m): ?>
                                    <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <?php 
                                foreach ($categories ?? [] as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Language</label>
                            <select name="language" class="form-select">
                                <option value="zh-CN">Chinese</option>
                                <option value="en-US">English</option>
                                <option value="ja-JP">Japanese</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Region</label>
                            <select name="region" class="form-select">
                                <option value="CN">CN</option>
                                <option value="US">US</option>
                                <option value="JP">JP</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Topic</label>
                        <input type="text" name="topic" class="form-control" placeholder="e.g. Latest AI trends in 2025">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Custom Prompt (optional, use {topic} placeholder)</label>
                        <textarea name="prompt" class="form-control" rows="3" placeholder="Write an article about {topic}..."></textarea>
                    </div>
                </div>

                <button type="submit" name="create" value="1" class="btn btn-primary">Create Schedule</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('task-type').addEventListener('change', function() {
    var pub = document.getElementById('publish-fields');
    var gen = document.getElementById('generate-fields');
    if (this.value === 'publish_article') { pub.classList.remove('d-none'); gen.classList.add('d-none'); }
    else { pub.classList.add('d-none'); gen.classList.remove('d-none'); }
});
</script>
