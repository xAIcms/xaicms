<?php
// templates/admin/plugins_list.php
// Plugin management page

require_once __DIR__ . '/../../src/Core/Plugin.php';

// Handle activation/deactivation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['slug'])) {
    if ($_POST['action'] === 'activate') {
        Plugin::activate($_POST['slug']);
    } elseif ($_POST['action'] === 'deactivate') {
        Plugin::deactivate($_POST['slug']);
    }
    header('Location: /admin/plugins');
    exit;
}

$allPlugins = Plugin::scan();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Plugins</h1>
    </div>

    <?php if (empty($allPlugins)): ?>
        <div class="alert alert-info">
            No plugins found. Place plugin folders in <code>/plugins/</code> directory.
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($allPlugins as $slug => $plugin): ?>
        <div class="col-md-6 mb-3">
            <div class="card <?php echo $plugin['active'] ? 'border-primary' : ''; ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1">
                                <?php echo htmlspecialchars($plugin['name']); ?>
                                <?php if ($plugin['active']): ?>
                                    <span class="badge bg-success ms-1">Active</span>
                                <?php endif; ?>
                            </h5>
                            <p class="text-muted small mb-2">
                                v<?php echo htmlspecialchars($plugin['version']); ?>
                                by <?php echo htmlspecialchars($plugin['author']); ?>
                            </p>
                            <p class="card-text"><?php echo htmlspecialchars($plugin['description']); ?></p>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="slug" value="<?php echo $slug; ?>">
                            <?php if ($plugin['active']): ?>
                                <input type="hidden" name="action" value="deactivate">
                                <button type="submit" class="btn btn-outline-danger btn-sm">Deactivate</button>
                            <?php else: ?>
                                <input type="hidden" name="action" value="activate">
                                <button type="submit" class="btn btn-outline-primary btn-sm">Activate</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
