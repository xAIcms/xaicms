<?php ob_start(); ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-gray-800">分类管理</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/admin/categories/create" class="btn btn-sm btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> 新建分类
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger bg-opacity-10 text-danger" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted">
                    <tr>
                        <th class="ps-4" style="width: 30%;">分类名称</th>
                        <th style="width: 25%;">Slug</th>
                        <th style="width: 15%;">排序</th>
                        <th style="width: 15%;">文章数</th>
                        <th class="text-end pe-4" style="width: 15%;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-folder2-open fs-1 d-block mb-2"></i>
                            暂无分类
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php
                        // Helper to build tree
                        function buildTree(array $elements, $parentId = 0) {
                            $branch = array();
                            foreach ($elements as $element) {
                                if ($element['parent_id'] == $parentId) {
                                    $children = buildTree($elements, $element['id']);
                                    if ($children) {
                                        $element['children'] = $children;
                                    }
                                    $branch[] = $element;
                                }
                            }
                            return $branch;
                        }

                        // Helper to render tree
                        function renderTree($tree, $level = 0) {
                            foreach ($tree as $item) {
                                echo '<tr>';
                                echo '<td class="ps-4">';
                                if ($level > 0) {
                                    echo '<span class="text-muted me-2" style="margin-left: ' . ($level * 20) . 'px;">└─</span>';
                                }
                                echo '<span class="fw-bold text-dark">' . htmlspecialchars($item['name']) . '</span>';
                                echo '</td>';
                                echo '<td class="text-secondary">' . htmlspecialchars($item['slug']) . '</td>';
                                echo '<td><span class="badge bg-light text-secondary border fw-normal">' . htmlspecialchars($item['sort_order']) . '</span></td>';
                                echo '<td><span class="badge bg-secondary rounded-pill px-2">' . ($item['article_count'] ?? 0) . '</span></td>';
                                echo '<td class="text-end pe-4">';
                                echo '<div class="btn-group">';
                                echo '<a href="/admin/categories/edit/' . $item['id'] . '" class="btn btn-sm btn-white text-primary border shadow-sm" data-bs-toggle="tooltip" title="编辑"><i class="bi bi-pencil"></i></a>';
                                echo '<button type="button" class="btn btn-sm btn-white text-danger border shadow-sm ms-1" onclick="deleteCategory(' . $item['id'] . ')" data-bs-toggle="tooltip" title="删除"><i class="bi bi-trash"></i></button>';
                                echo '</div>';
                                echo '</td>';
                                echo '</tr>';
                                
                                if (isset($item['children'])) {
                                    renderTree($item['children'], $level + 1);
                                }
                            }
                        }

                        $tree = buildTree($categories);
                        renderTree($tree);
                        ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })

function deleteCategory(id) {

    if (confirm('确定要删除这个分类吗？')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/categories/delete/' + id;
        
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
