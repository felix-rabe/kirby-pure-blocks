<?php
/**
 * FILE: /site/snippets/organizer/thumbnail.php
 */
$organizerId       = (string)($grid['organizerId'] ?? uniqid('organizer-', true));
$rowGap            = (string)($grid['rowGap'] ?? $grid['gap'] ?? 's');
$columnGap         = (string)($grid['columnGap'] ?? $grid['gap'] ?? 's');
$enableTagFilter   = (bool)($grid['enableTagFilter'] ?? false);
$enableGroupFilter = (bool)($grid['enableGroupFilter'] ?? false);
$enableDateFilter  = (bool)($grid['enableDateFilter'] ?? false);
$enableFilterMulti = (bool)($grid['enableFilterMulti'] ?? false);
$enableItemScaler  = (bool)($grid['enableItemScaler'] ?? false);
$isSticky          = (bool)($grid['filterSticky'] ?? false);
$stickyTop         = (string)($grid['stickyFilterTop'] ?? 'none');

$rowGapValue = $rowGap === 'none' ? '0px' : 'var(--size-' . $rowGap . ', 0px)';
$columnGapValue = $columnGap === 'none' ? '0px' : 'var(--size-' . $columnGap . ', 0px)';
$grid['rowGapValue'] = $rowGapValue;
$grid['columnGapValue'] = $columnGapValue;

$scopeClass = 'thumbnail-scope';
?>
<section class="<?= $scopeClass ?>">

  <?php snippet('organizer/filter-buttons', [
      'items'            => $subpages,
      'enabled'          => $enableTagFilter,
      'groupEnabled'     => $enableGroupFilter,
      'dateEnabled'      => $enableDateFilter,
      'multiSelect'      => $enableFilterMulti,
      'scopeClass'       => $scopeClass,
      'itemSelector'     => '.pure-organizer-item',
      'margin'           => $grid['marginFilterButtons'] ?? 'none',
      'enableItemScaler' => $enableItemScaler,
      'isSticky'         => $isSticky,
      'stickyTop'        => $stickyTop,
      'organizerId'      => $organizerId,
  ]); ?>

  <?php snippet('organizer/grid', [
      'items' => $subpages,
      'grid' => $grid,
      'groupKey' => 'all',
  ]); ?>

  <div class="pure-organizer-filter-empty text-align-center" hidden>
    <p>No items found.</p>
  </div>
</section>
