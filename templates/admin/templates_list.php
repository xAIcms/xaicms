<?php
// templates/admin/templates_list.php
// Template management page

require_once __DIR__ . '/../../src/Core/Template.php';

// Handle activation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['slug'])) {
    if ($_POST['action'] === 'activate') {
        Template::activate($_POST['slug']);
        header('Location: /admin/templates');
        exit;
    }
}

$allTemplates = Template::scan();
$currentSlug = Template::getCurrent();
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Templates</h1>
        <span class="text-muted">Install templates in <code>/templates/</code> directory</span>
    </div>

    <?php if (count($allTemplates) <= 1): ?>
        <div class="alert alert-info">
            Only the default template is installed. Drop new templates into the <code>/templates/</code> folder.
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($allTemplates as $slug => $tpl): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 <?php echo $tpl['active'] ? 'border-primary' : ''; ?>">
                <!-- Thumbnail -->
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:180px;overflow:hidden;">
                    <?php if (!empty($tpl['thumbnail'])): ?>
                        <img src="<?php echo htmlspecialchars($tpl['thumbnail']); ?>" alt="<?php echo htmlspecialchars($tpl['name']); ?>" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                            <p class="small mt-2">No preview</p>
                        </div>
                    <?php endif; ?>
                    <?php if ($tpl['active']): ?>
                        <span class="position-absolute top-0 end-0 badge bg-success m-2">Active</span>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($tpl['name']); ?></h5>
                    <p class="text-muted small mb-2">
                        v<?php echo htmlspecialchars($tpl['version']); ?>
                        by <?php echo htmlspecialchars($tpl['author']); ?>
                    </p>
                    <p class="card-text small"><?php echo htmlspecialchars($tpl['description']); ?></p>
                </div>

                <div class="card-footer bg-transparent">
                    <?php if (!$tpl['active']): ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="activate">
                            <input type="hidden" name="slug" value="<?php echo $slug; ?>">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Activate</button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-outline-secondary btn-sm w-100" disabled>Current Template</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
