<?php
$blocks = $site->headerNavigation()->toBlocks();
?>

<?php if ($blocks->isNotEmpty()): ?>
<header id="site-header">
    <?= $blocks ?>
</header>
<?php endif ?>