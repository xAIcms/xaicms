<?php
// php_backend/templates/admin/media_library.php

$title = '媒体库';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
    <div>
        <h1 class="h2 fw-bold text-gray-800">媒体库</h1>
        <p class="text-muted small mb-0">管理您的图片和文件资源</p>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-primary shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bi bi-cloud-upload me-1"></i> 上传文件
        </button>
        <button type="button" class="btn btn-sm btn-white border shadow-sm" data-bs-toggle="modal" data-bs-target="#categoryModal">
            <i class="bi bi-folder-plus me-1"></i> 新建分类
        </button>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-3 col-lg-2">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="bi bi-folder me-2 text-warning"></i>文件夹
                </h6>
            </div>
            <div class="list-group list-group-flush rounded-bottom-3">
                <a href="/admin/media" class="list-group-item list-group-item-action border-0 py-3 <?php echo empty($currentCategoryId) ? 'list-group-item-primary fw-bold' : 'text-secondary'; ?>">
                    <i class="bi bi-grid-fill me-2"></i> 全部媒体
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="/admin/media?category=<?php echo $cat['id']; ?>" class="list-group-item list-group-item-action border-0 py-3 d-flex justify-content-between align-items-center <?php echo ($currentCategoryId == $cat['id']) ? 'list-group-item-primary fw-bold' : 'text-secondary'; ?>">
                        <span class="text-truncate">
                            <i class="bi bi-folder2-open me-2"></i> <?php echo htmlspecialchars($cat['name']); ?>
                        </span>
                        <?php if ($currentCategoryId != $cat['id']): ?>
                        <button type="button" class="btn btn-link btn-sm p-0 text-muted hover-danger" onclick="deleteCategory(event, <?php echo $cat['id']; ?>)" title="删除分类">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-9 col-lg-10">
        <div class="card border-0 shadow-sm rounded-3 min-vh-100">
            <div class="card-body p-4">
                <?php if (empty($files)): ?>
                    <div class="text-center py-5 mt-5">
                        <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                            <i class="bi bi-images display-4 text-secondary opacity-50"></i>
                        </div>
                        <h5 class="text-muted fw-normal">暂无媒体文件</h5>
                        <p class="text-muted small mb-4">点击"上传文件"按钮添加您的第一个文件</p>
                        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bi bi-cloud-upload me-1"></i> 立即上传
                        </button>
                    </div>
                <?php else: ?>
                    <div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4">
                        <?php foreach ($files as $file): ?>
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm file-card group-action-card transition-hover">
                                    <div class="ratio ratio-1x1 bg-light rounded-top-3 overflow-hidden position-relative border-bottom">
                                        <?php if (strpos($file['mime_type'], 'image') !== false): ?>
                                            <img src="<?php echo $file['path']; ?>" class="card-img-top object-fit-cover transition-transform" alt="<?php echo htmlspecialchars($file['original_name']); ?>" loading="lazy">
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center h-100 text-secondary">
                                                <i class="bi bi-file-earmark-text display-4 opacity-50"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Hover Actions -->
                                        <div class="file-actions position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center opacity-0 transition-opacity">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-light btn-sm rounded-circle mx-1" onclick="copyUrl('<?php echo $file['path']; ?>')" title="复制链接" data-bs-toggle="tooltip">
                                                    <i class="bi bi-link-45deg"></i>
                                                </button>
                                                <a href="<?php echo $file['path']; ?>" target="_blank" class="btn btn-light btn-sm rounded-circle mx-1" title="查看原图" data-bs-toggle="tooltip">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm rounded-circle mx-1" onclick="deleteFile(<?php echo $file['id']; ?>)" title="删除文件" data-bs-toggle="tooltip">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-2 bg-white rounded-bottom-3">
                                        <p class="card-text text-truncate small fw-medium text-dark mb-1" title="<?php echo htmlspecialchars($file['original_name']); ?>">
                                            <?php echo htmlspecialchars($file['original_name']); ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted x-small"><?php echo round($file['size'] / 1024); ?> KB</small>
                                            <small class="text-muted x-small text-uppercase"><?php echo pathinfo($file['original_name'], PATHINFO_EXTENSION); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="d-flex justify-content-center mt-5">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-sm shadow-sm rounded-pill overflow-hidden">
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link border-0 <?php echo $i == $page ? 'bg-primary text-white' : 'text-secondary bg-white'; ?> px-3 py-2" href="?page=<?php echo $i; ?>&category=<?php echo $currentCategoryId; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">上传文件</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="uploadForm" method="POST" action="/admin/media/upload" enctype="multipart/form-data">
                    <?php echo Csrf::input(); ?>
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">选择分类</label>
                        <select name="category_id" class="form-select border-0 bg-light py-2">
                            <option value="">无分类</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($currentCategoryId == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">选择文件</label>
                        <div class="upload-area p-5 border-2 border-dashed border-secondary border-opacity-25 rounded-3 text-center bg-light cursor-pointer position-relative">
                            <input type="file" name="files[]" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" multiple required onchange="updateFileName(this)">
                            <i class="bi bi-cloud-arrow-up display-4 text-primary mb-3 d-block"></i>
                            <p class="mb-0 fw-medium text-dark">点击或拖拽文件到此处</p>
                            <p class="small text-muted mb-0" id="fileNameDisplay">支持多文件上传</p>
                        </div>
                    </div>
                    <div class="progress mb-3 d-none rounded-pill" id="uploadProgress" style="height: 6px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2 rounded-pill shadow-sm">
                            <i class="bi bi-upload me-1"></i> 开始上传
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">添加分类</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" action="/admin/media/category/create">
                    <?php echo Csrf::input(); ?>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">分类名称</label>
                        <input type="text" name="name" class="form-control border-0 bg-light py-2" required placeholder="例如: 文章配图">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">Slug (可选)</label>
                        <input type="text" name="slug" class="form-control border-0 bg-light py-2" placeholder="例如: article-images">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary py-2 rounded-pill shadow-sm">添加</button>
                    </div>
                </form>
            </div>
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

function updateFileName(input) {
    const display = document.getElementById('fileNameDisplay');
    if (input.files && input.files.length > 0) {
        if (input.files.length === 1) {
            display.textContent = input.files[0].name;
        } else {
            display.textContent = `已选择 ${input.files.length} 个文件`;
        }
        display.classList.add('text-primary', 'fw-bold');
        display.classList.remove('text-muted');
    } else {
        display.textContent = '支持多文件上传';
        display.classList.remove('text-primary', 'fw-bold');
        display.classList.add('text-muted');
    }
}

function copyUrl(path) {
    const fullUrl = window.location.origin + path;
    navigator.clipboard.writeText(fullUrl).then(() => {
        // You could add a toast here like in api_list.php
        alert('链接已复制到剪贴板');
    });
}

function deleteFile(id) {
    if (confirm('确定要删除此文件吗？')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/media/delete';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        form.appendChild(input);

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = 'csrf_token';
        csrf.value = '<?php echo Csrf::generate(); ?>';
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
    }
}

function deleteCategory(e, id) {
    e.preventDefault();
    if (confirm('确定要删除此分类吗？分类下的文件将变为未分类。')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/media/category/delete';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        form.appendChild(input);

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = 'csrf_token';
        csrf.value = '<?php echo Csrf::generate(); ?>';
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
