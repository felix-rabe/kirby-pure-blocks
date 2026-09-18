<?php
$blocks = $site->footerNavigation()->toBlocks();
?>

<?php if ($blocks->isNotEmpty()): ?>
<footer id="site-footer">
    <?= $blocks ?>
</footer>
<?php endif ?>