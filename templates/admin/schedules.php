<?php
// templates/admin/schedules.php
// Task scheduler — create, edit, delete scheduled tasks
// Supports BT Panel-style time settings for full cron flexibility

require_once __DIR__ . '/../../src/Core/Scheduler.php';

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $taskName = $_POST['task_name'] ?? 'generate_article';
        $desc = $_POST['description'] ?? '';
        $config = [];

        // Calculate interval in seconds from BT Panel-style inputs
        $intervalMode = $_POST['interval_mode'] ?? 'daily';
        $intervalSeconds = match ($intervalMode) {
            'minutes' => (int)($_POST['min_val'] ?? 30) * 60,
            'hours' => (int)($_POST['hour_val'] ?? 1) * 3600,
            'daily' => 86400,
            'weekly' => 604800,
            'monthly' => 2592000,
            'custom' => (int)($_POST['custom_seconds'] ?? 86400),
            default => 86400,
        };

        // Specific time of day (HH:MM) — only for daily/weekly
        $runAtTime = $_POST['run_at_time'] ?? '';
        if ($runAtTime && in_array($intervalMode, ['daily', 'weekly'])) {
            $config['run_at_time'] = $runAtTime;
        }
        // Specific days for weekly
        if ($intervalMode === 'weekly') {
            $config['run_on_days'] = $_POST['week_days'] ?? [];
        }

        if ($taskName === 'publish_article') {
            $config['article_id'] = (int)($_POST['article_id'] ?? 0);
        } elseif ($taskName === 'generate_article') {
            $config['model_id'] = (int)($_POST['model_id'] ?? 0);
            $config['category_id'] = (int)($_POST['category_id'] ?? 0);
            $config['language'] = $_POST['language'] ?? 'zh-CN';
            $config['region'] = $_POST['region'] ?? 'CN';
            $config['topic'] = $_POST['topic'] ?? '';
            $config['keywords'] = $_POST['keywords'] ?? '';
            $config['tone'] = $_POST['tone'] ?? 'professional';
            $config['word_count'] = (int)($_POST['word_count'] ?? 800);
            $config['author_name'] = $_POST['author_name'] ?? 'AI Assistant';
            $config['prompt'] = $_POST['prompt'] ?? '';
        }

        Scheduler::create($taskName, $config, $intervalSeconds, $desc);
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

function intervalLabel(int $seconds): string {
    if ($seconds < 3600) return "Every {$seconds} seconds";
    if ($seconds < 86400) return 'Every ' . ($seconds / 3600) . ' hours';
    if ($seconds === 86400) return 'Daily';
    if ($seconds <= 604800) return 'Every ' . ($seconds / 86400) . ' days';
    return 'Every ' . ($seconds / 86400) . ' days';
}

$aiModels = class_exists('AiModel') ? AiModel::getAll() : [];
$articles = class_exists('Article') ? Article::getAll(200, 0) : [];
$categories = class_exists('Category') ? Category::getAll() : [];
?>

<div class="container-fluid">
    <h1 class="mb-4">Task Scheduler</h1>

    <?php if ($message): ?><div class="alert alert-success"><?php echo $message; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>

    <div class="alert alert-info">
        <strong>No cron job needed.</strong> Tasks run automatically when visitors browse your site.
    </div>

    <!-- Existing tasks -->
    <div class="card mb-4">
        <div class="card-header"><strong>Active Schedules</strong></div>
        <div class="card-body p-0">
            <?php if (empty($schedules)): ?>
                <div class="text-center py-4 text-muted">No scheduled tasks yet.</div>
            <?php else: ?>
                <table class="table mb-0">
                    <thead><tr><th>Task</th><th>Details</th><th>Interval</th><th>Next Run</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($schedules as $s):
                        $cfg = json_decode($s['config'] ?? '{}', true) ?: [];
                        $detail = $s['task_name'] === 'generate_article'
                            ? ('Topic: ' . ($cfg['topic'] ?? '—'))
                            : ('Article #' . ($cfg['article_id'] ?? 0));
                    ?>
                        <tr class="<?php echo $s['enabled'] ? '' : 'text-muted'; ?>">
                            <td><?php echo $s['task_name'] === 'generate_article' ? '🤖' : '📅'; ?> <?php echo htmlspecialchars($s['task_name']); ?></td>
                            <td class="small"><?php echo htmlspecialchars($detail); ?></td>
                            <td><?php echo intervalLabel($s['interval_seconds']); ?></td>
                            <td class="small"><?php echo $s['next_run_at']; ?></td>
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
                            <option value="generate_article" selected>Generate Article (AI)</option>
                            <option value="publish_article">Publish Draft</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" placeholder="e.g. Daily AI tech post">
                    </div>
                </div>

                <!-- === Time Settings (BT Panel style) === -->
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6>Frequency</h6>
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <select name="interval_mode" id="interval-mode" class="form-select">
                                    <option value="minutes">Every N minutes</option>
                                    <option value="hours">Every N hours</option>
                                    <option value="daily" selected>Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="custom">Custom (seconds)</option>
                                </select>
                            </div>
                            <div class="col-md-2" id="min-val-wrap">
                                <input type="number" name="min_val" class="form-control" value="30" min="1" max="59" placeholder="30">
                                <div class="form-text">Minutes</div>
                            </div>
                            <div class="col-md-2 d-none" id="hour-val-wrap">
                                <input type="number" name="hour_val" class="form-control" value="1" min="1" max="23" placeholder="1">
                                <div class="form-text">Hours</div>
                            </div>
                            <div class="col-md-2 d-none" id="custom-sec-wrap">
                                <input type="number" name="custom_seconds" class="form-control" value="86400" min="60" placeholder="86400">
                                <div class="form-text">Seconds</div>
                            </div>
                        </div>

                        <!-- Run at specific time (daily/weekly) -->
                        <div class="row mt-2" id="run-at-time-wrap">
                            <div class="col-md-3">
                                <label class="form-label">Run at</label>
                                <input type="time" name="run_at_time" class="form-control" value="08:00">
                            </div>
                            <div class="col-md-6 d-none" id="week-days-wrap">
                                <label class="form-label">On days</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $i => $d): ?>
                                        <label class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="week_days[]" value="<?php echo $i; ?>" <?php echo $i < 5 ? 'checked' : ''; ?>><?php echo $d; ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- === Publish Article fields === -->
                <div id="publish-fields" class="d-none row mb-3">
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

                <!-- === Generate Article fields === -->
                <div id="generate-fields">
                    <h6 class="mb-3">Content Settings</h6>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Author Name <span class="text-danger">*</span></label>
                            <input type="text" name="author_name" class="form-control" value="AI Editor" required>
                            <div class="form-text">Displayed as article author on frontend.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <?php foreach ($categories as $c): ?>
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
                                <option value="ko-KR">Korean</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Region</label>
                            <select name="region" class="form-select">
                                <option value="CN">CN</option><option value="US">US</option><option value="JP">JP</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Topic / Title <span class="text-danger">*</span></label>
                            <input type="text" name="topic" class="form-control" required placeholder="e.g. Latest AI trends in 2025">
                            <div class="form-text">The AI will write about this topic. Be specific.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Keywords (comma-separated)</label>
                            <input type="text" name="keywords" class="form-control" placeholder="e.g. AI, machine learning, future tech">
                            <div class="form-text">Target keywords for SEO optimization.</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Writing Tone</label>
                            <select name="tone" class="form-select">
                                <option value="professional">Professional</option>
                                <option value="casual">Casual / Conversational</option>
                                <option value="technical">Technical / In-depth</option>
                                <option value="journalistic">Journalistic / News</option>
                                <option value="persuasive">Persuasive / Sales</option>
                                <option value="educational">Educational / Tutorial</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Word Count</label>
                            <select name="word_count" class="form-select">
                                <option value="400">Short (~400 words)</option>
                                <option value="800" selected>Medium (~800 words)</option>
                                <option value="1500">Long (~1500 words)</option>
                                <option value="3000">Comprehensive (~3000 words)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">AI Model</label>
                            <select name="model_id" class="form-select">
                                <?php foreach ($aiModels as $m): ?>
                                    <option value="<?php echo $m['id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Custom Prompt <small class="text-muted">(optional, overrides topic/word count when specified)</small></label>
                        <textarea name="prompt" class="form-control" rows="4" placeholder="Write an article about {topic}. Include these keywords: {keywords}. Use a {tone} tone. Target {word_count} words."></textarea>
                        <div class="form-text">Use <code>{topic}</code> <code>{keywords}</code> <code>{tone}</code> <code>{word_count}</code> as placeholders.</div>
                    </div>
                </div>

                <button type="submit" name="create" value="1" class="btn btn-primary btn-lg">Create Schedule</button>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle task type fields
document.getElementById('task-type').addEventListener('change', function() {
    document.getElementById('publish-fields').classList.toggle('d-none', this.value !== 'publish_article');
    document.getElementById('generate-fields').classList.toggle('d-none', this.value !== 'generate_article');
});

// Toggle interval sub-fields
var mode = document.getElementById('interval-mode');
mode.addEventListener('change', function() {
    document.getElementById('min-val-wrap').classList.toggle('d-none', this.value !== 'minutes');
    document.getElementById('hour-val-wrap').classList.toggle('d-none', this.value !== 'hours');
    document.getElementById('custom-sec-wrap').classList.toggle('d-none', this.value !== 'custom');
    document.getElementById('run-at-time-wrap').classList.toggle('d-none', this.value !== 'daily' && this.value !== 'weekly');
    document.getElementById('week-days-wrap').classList.toggle('d-none', this.value !== 'weekly');
});
mode.dispatchEvent(new Event('change'));
</script>
