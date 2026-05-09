<?php
// templates/admin/update.php
// Online update page

require_once __DIR__ . '/../../src/Core/Updater.php';

$updater = new Updater();
$check = $updater->check();
$message = '';
$error = '';

// Handle update action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_update'])) {
    $result = $updater->apply();
    if ($result['success']) {
        $message = $result['message'];
        $check = $updater->check(); // Refresh
    } else {
        $error = $result['message'];
    }
}

// Handle re-check (clear cache)
if (isset($_GET['force'])) {
    $cacheFile = sys_get_temp_dir() . '/xaicms_update_check.json';
    if (file_exists($cacheFile)) unlink($cacheFile);
    $check = $updater->check();
    header('Location: /admin/update');
    exit;
}
?>

<div class="container-fluid">
    <h1 class="mb-4">System Update</h1>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (!empty($check['error'])): ?>
        <div class="alert alert-warning">Update check failed: <?php echo htmlspecialchars($check['error']); ?>. <a href="?force=1">Retry</a></div>
    <?php endif; ?>

    <!-- Version Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Current Version</h6>
                    <h2 class="mb-0">v<?php echo htmlspecialchars($check['current']); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Latest Version</h6>
                    <h2 class="mb-0 <?php echo $check['has_update'] ? 'text-success' : ''; ?>">
                        v<?php echo htmlspecialchars($check['latest']); ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Status</h6>
                    <?php if ($check['has_update']): ?>
                        <h2 class="mb-0 text-warning">Update Available</h2>
                    <?php else: ?>
                        <h2 class="mb-0 text-success">Up to Date</h2>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($check['has_update']): ?>
        <!-- Update Details -->
        <div class="card mb-4">
            <div class="card-header">
                <strong>What's New in v<?php echo htmlspecialchars($check['latest']); ?></strong>
            </div>
            <div class="card-body">
                <pre class="mb-0" style="white-space:pre-wrap;font-size:14px;"><?php echo htmlspecialchars($check['changelog'] ?: 'No changelog provided.'); ?></pre>
            </div>
        </div>

        <!-- Update Action -->
        <div class="card border-warning">
            <div class="card-body">
                <h5 class="text-warning">Update from v<?php echo htmlspecialchars($check['current']); ?> to v<?php echo htmlspecialchars($check['latest']); ?></h5>
                <p class="text-muted">Your data and configuration will be preserved. A backup will be made before update.</p>
                <form method="POST" onsubmit="return confirm('Are you sure you want to update? A backup will be created automatically.');">
                    <button type="submit" name="do_update" value="1" class="btn btn-warning btn-lg">
                        Update Now
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="text-end mt-3">
        <small class="text-muted">
            Last checked: <?php echo date('Y-m-d H:i:s', $check['checked_at'] ?? time()); ?>
            <a href="?force=1" class="ms-2">Check again</a>
        </small>
    </div>
</div>
