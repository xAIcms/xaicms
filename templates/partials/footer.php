<?php include __DIR__ . '/footer_content.php'; ?>
<?php include __DIR__ . '/back_to_top.php'; ?>

<?php do_action('before_footer'); ?>

<script src="/assets/js/custom.js?v=<?php echo time(); ?>"></script>
<script src="/assets/vendor/translate/translate.js"></script>
<script>
    // Initialize translate.js after load
    if (typeof initTranslate === 'function') {
        initTranslate();
    }
</script>

<!-- Custom JS -->
<?php if (!empty($settings['customJs'])): ?>
<script>
    <?php echo $settings['customJs']; ?>
</script>
<?php endif; ?>

</body>
</html>