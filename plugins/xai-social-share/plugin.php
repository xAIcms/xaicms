<?php
/**
 * Plugin Name: 社交分享
 * Description: 文章页一键分享到Twitter/Facebook/LinkedIn
 * Version: 1.0.0
 * Author: xAI
 * Price: 0
 */
add_action('before_footer', function() {
    $url = ($_SERVER['HTTPS'] ?? '') === 'on' ? 'https://' : 'http://';
    $url .= $_SERVER['HTTP_HOST'] . ($_SERVER['REQUEST_URI'] ?? '/');
    echo '<div class="social-share" style="position:fixed;left:20px;top:50%;transform:translateY(-50%);z-index:40;display:flex;flex-direction:column;gap:8px;">';
    echo '<a href="https://twitter.com/intent/tweet?url='.urlencode($url).'" target="_blank" class="w-10 h-10 bg-blue-400 text-white rounded-full flex items-center justify-center hover:bg-blue-500" title="Twitter"><i class="bi bi-twitter-x"></i></a>';
    echo '<a href="https://www.facebook.com/sharer/sharer.php?u='.urlencode($url).'" target="_blank" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700" title="Facebook"><i class="bi bi-facebook"></i></a>';
    echo '<a href="https://www.linkedin.com/sharing/share-offsite/?url='.urlencode($url).'" target="_blank" class="w-10 h-10 bg-blue-700 text-white rounded-full flex items-center justify-center hover:bg-blue-800" title="LinkedIn"><i class="bi bi-linkedin"></i></a>';
    echo '</div>';
});
