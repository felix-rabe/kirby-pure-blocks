<?php if ($page->blocks()->isNotEmpty()): ?>

<?php
$hasMaxBlocksWidth   = $page->maxBlocksWidthToggle()->toBool();
$maxBlocksWidthValue = $page->maxBlocksWidthValue()->isNotEmpty()
  ? $page->maxBlocksWidthValue()->int()
  : null;

$maxBlocksAlignment  = $page->maxBlocksWidthAlignment()->or('center')->value();

// Fetch the padding values
$blocksPaddingLR     = $page->blocksPaddingLeftRight()->or('none')->value();
$blocksPaddingTop    = $page->blocksPaddingTop()->or('none')->value();

$wrapperClasses = ['pure-blocks-wrapper'];

// ADDED: Apply the top padding class to the wrapper instead of the blocks
if ($blocksPaddingTop !== 'none') {
    $wrapperClasses[] = 'padding-top-' . esc($blocksPaddingTop);
}
?>

<div class="<?= implode(' ', $wrapperClasses) ?>">
  <?php foreach ($page->blocks()->toBlocks() as $block): ?>
    <?php snippet('pure-blocks-wrapper', [
      'block' => $block,
      'maxBlocksWidthEnabled' => $hasMaxBlocksWidth,
      'maxBlocksWidthValue'   => $maxBlocksWidthValue,
      'maxBlocksAlignment'    => $maxBlocksAlignment,
      'blocksPaddingLR'       => $blocksPaddingLR,
      // Removed blocksPaddingTop from here as it's now on the wrapper
    ]) ?>
  <?php endforeach ?>
</div>

<?php endif ?>