<?php
// templates/partials/pagination.php
if (!isset($baseUrl)) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $parsedUrl = parse_url($requestUri);
    $path = $parsedUrl['path'] ?? '/';
    $queryParams = [];
    
    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $queryParams);
    }
    
    // Remove 'page' parameter to prevent duplication
    if (isset($queryParams['page'])) {
        unset($queryParams['page']);
    }
    
    // Rebuild URL
    if (!empty($queryParams)) {
        $baseUrl = $path . '?' . http_build_query($queryParams);
    } else {
        $baseUrl = $path;
    }
}

if (isset($totalPages) && $totalPages > 1): 
    $connector = (strpos($baseUrl, '?') !== false) ? '&' : '?';
?>
    <div class="mt-12 flex justify-center space-x-2">
        <?php if ($page > 1): ?>
            <a href="<?php echo $baseUrl . $connector . 'page=' . ($page - 1); ?>" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-indigo-600 transition-colors">
                <i class="bi bi-chevron-left"></i>
            </a>
        <?php endif; ?>

        <?php 
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        
        if ($start > 1): ?>
            <a href="<?php echo $baseUrl . $connector . 'page=1'; ?>" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-indigo-600 transition-colors">1</a>
            <?php if ($start > 2): ?>
                <span class="px-4 py-2 text-gray-400">...</span>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start; $i <= $end; $i++): ?>
            <a href="<?php echo $baseUrl . $connector . 'page=' . $i; ?>" 
               class="px-4 py-2 border rounded-lg font-medium transition-colors
                      <?php echo $i == $page ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-500/20' : 'border-gray-300 text-gray-600 hover:bg-gray-50 hover:text-indigo-600'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
        
        <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?>
                <span class="px-4 py-2 text-gray-400">...</span>
            <?php endif; ?>
            <a href="<?php echo $baseUrl . $connector . 'page=' . $totalPages; ?>" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-indigo-600 transition-colors"><?php echo $totalPages; ?></a>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
            <a href="<?php echo $baseUrl . $connector . 'page=' . ($page + 1); ?>" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-indigo-600 transition-colors">
                <i class="bi bi-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>
