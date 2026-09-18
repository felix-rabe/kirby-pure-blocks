<?php
/** @var \Kirby\Cms\Block $block */
/** @var string $blocksPaddingLR */

$extraClasses = $extraClasses ?? [];
$extraAttrs   = $extraAttrs ?? [];

$maxBlocksWidthEnabled = $maxBlocksWidthEnabled ?? false;
$maxBlocksWidthValue   = $maxBlocksWidthValue ?? null;
$maxBlocksAlignment    = $maxBlocksAlignment ?? 'center';
$blocksPaddingLR       = $blocksPaddingLR ?? 'none';

// Use the editor-defined anchor for public links and keep Kirby's block ID
// as a fallback for blocks without an anchor.
$anchorValue = ltrim(trim((string)$block->anchor()->value()), '#');
$blockId     = $anchorValue !== '' ? $anchorValue : $block->id();

// margin-bottom from block field
$mb = $block->marginBottom()->or('none')->value();
$mbClass = ($mb !== 'none') ? 'margin-bottom-' . esc($mb) : '';

$mlr = $block->marginLeftRight()->or('none')->value();
$mlrClass = ($mlr !== 'none') ? 'margin-left-right-' . esc($mlr) : '';

// Generate the padding class for Left/Right (since you still want this on blocks)
$paddingLRClass = ($blocksPaddingLR !== 'none') ? 'padding-left-right-' . esc($blocksPaddingLR) : '';

// block-level full bleed toggle
$isFullBleed = $block->fullBleed()->toBool();

$classes = array_filter(array_merge([
  'block',
  'block-type-' . $block->type(),
  $mbClass,
  $mlrClass,
  $paddingLRClass,
  // REMOVED: $paddingTopClass
  $isFullBleed ? 'block-full-bleed' : null,
], $extraClasses));

$attrString = '';
foreach ($extraAttrs as $k => $v) {
  if ($v === true) {
    $attrString .= ' ' . esc($k);
  } elseif ($v !== null && $v !== false && $v !== '') {
    $attrString .= ' ' . esc($k) . '="' . esc((string)$v) . '"';
  }
}

$innerClasses = ['block-inner'];
$innerStyle   = '';

if (!$isFullBleed && $maxBlocksWidthEnabled && $maxBlocksWidthValue) {
  $innerClasses[] = 'align-' . esc($maxBlocksAlignment);
  $innerStyle = 'max-width:' . (int)$maxBlocksWidthValue . 'px;';
}
?>

<div id="<?= esc($blockId, 'attr') ?>" class="<?= implode(' ', $classes) ?>"<?= $attrString ?>>
  <?php if ($isFullBleed): ?>
    <?php snippet('blocks/' . $block->type(), ['block' => $block]) ?>
  <?php else: ?>
    <div
      class="<?= implode(' ', $innerClasses) ?>"
      <?= $innerStyle ? 'style="' . esc($innerStyle) . '"' : '' ?>
    >
      <?php snippet('blocks/' . $block->type(), ['block' => $block]) ?>
    </div>
  <?php endif ?>
</div>
