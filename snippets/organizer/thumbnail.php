<?php
/**
 * FILE: /site/snippets/organizer/thumbnail.php
 */

$organizerId       = (string)($grid['organizerId'] ?? uniqid('organizer-', true));
$gap               = (string)($grid['gap'] ?? 's');
$alignItems        = (string)($grid['alignItems'] ?? 'start');
$justifyContent    = (string)($grid['justifyContent'] ?? 'flex-start');
$enableTagFilter   = (bool)($grid['enableTagFilter'] ?? false);
$enableDateFilter  = (bool)($grid['enableDateFilter'] ?? false);
$enableFilterMulti = (bool)($grid['enableFilterMulti'] ?? false);
$layoutMode        = (string)($grid['layoutMode'] ?? 'grid');
$enableItemScaler  = (bool)($grid['enableItemScaler'] ?? false);
$showTags          = (bool)($grid['showTags'] ?? false);
$showPageTitle     = (bool)($grid['showPageTitle'] ?? false);
$showMeta          = $showTags || $showPageTitle;

$isSticky          = (bool)($grid['filterSticky'] ?? false);
$stickyTop         = (string)($grid['stickyFilterTop'] ?? 'none');

$gapValue = $gap === 'none' ? '0px' : 'var(--size-' . $gap . ', 0px)';


$scopeClass = 'thumbnail-scope';

$organizerClasses = 'pure-organizer';
?>

<section class="<?= $scopeClass ?>">

  <?php snippet('organizer/filter-buttons', [
      'items'           => $subpages,
      'enabled'         => $enableTagFilter,
      'dateEnabled'     => $enableDateFilter,
      'multiSelect'     => $enableFilterMulti,
      'scopeClass'      => $scopeClass,
      'itemSelector'    => '.pure-organizer-item',
      'margin'          => $grid['marginFilterButtons'] ?? 'none',
      'enableItemScaler'=> $enableItemScaler,
      'isSticky'        => $isSticky,  // Passed to snippet
      'stickyTop'       => $stickyTop, // Passed to snippet
      'organizerId'     => $organizerId,
  ]); ?>

  <div class="<?= $organizerClasses ?>"
       style="--organizer-gap: <?= htmlspecialchars($gapValue, ENT_QUOTES, 'UTF-8') ?>;"
       data-layout="<?= htmlspecialchars($layoutMode, ENT_QUOTES, 'UTF-8') ?>"
       data-organizer-id="<?= htmlspecialchars($organizerId, ENT_QUOTES, 'UTF-8') ?>">

    <?php foreach ($subpages as $subpage): 
        $thumbnailWidth = $subpage->thumbnailWidth()->or(3)->toInt();
        $widthClass = 'has-' . $thumbnailWidth . '-spans';

        $tags = fieldTags($subpage, 'tags');
        $tagClasses = array_map(fn($t) => 'tag-' . str_replace(' ', '-', strtolower(trim($t))), $tags);
        
        $year = $subpage->date()->isNotEmpty() ? $subpage->date()->toDate('Y') : '';
        $yearClass = $year ? 'year-' . $year : '';

        $childItemClasses = classList([
            'pure-organizer-item',
            $widthClass,
            implode(' ', $tagClasses),
            $yearClass
        ]);

        $innerWrapperClasses = classList([
            'item-inner-wrapper',
            'text-block',
            $showMeta ? 'show-meta' : '',
        ]);
    ?>
      <div class="<?= $childItemClasses ?>"
               <?= pureOnScrollAttribute() ?>
           data-tags="<?= htmlspecialchars(implode(',', $tags), ENT_QUOTES, 'UTF-8') ?>"
           data-year="<?= htmlspecialchars((string)$year, ENT_QUOTES, 'UTF-8') ?>"
           data-span="<?= $thumbnailWidth ?>">
        
        <div class="<?= $innerWrapperClasses ?>">
            <?php if ($subpage->thumbnail()->isNotEmpty()): ?>
              <?php foreach ($subpage->thumbnail()->toBlocks() as $thumbBlock): ?>
                <?php snippet('pure-blocks-wrapper', [
                    'block' => $thumbBlock,
                    'extraClasses' => ['thumbnail-block']
                ]) ?>
              <?php endforeach ?>
            <?php else: ?>
              <div class="pure-organizer-no-thumbnail">No thumbnail found</div>
            <?php endif ?>
            
            <?php if ($showMeta): ?>
                <div class="meta-wrapper">
                    <?php if ($showTags): ?>
                        <div class="meta-infos-wrapper">
                            <?php if (!empty($tags)): ?>
                                <div class="meta-tags">
                                    <?php foreach ($tags as $tag): ?>
                                        <div class="btn mini-btn tag"><?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php endforeach ?>
                                </div>
                            <?php endif ?>

                            <?php if ($year): ?>
                                <div class="meta-year">
                                    <div class="btn mini-btn year"><?= htmlspecialchars($year, ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                            <?php endif ?>
                        </div>
                    <?php endif ?>

                    <?php if ($showPageTitle): ?>
                        <div class="meta-title">
                            <div class="btn"><?= htmlspecialchars($subpage->title()->value(), ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    <?php endif ?>
                </div>
            <?php endif ?>
        </div>
      </div>
    <?php endforeach ?>
  </div>

  <div class="pure-organizer-filter-empty text-align-center" hidden>
    <p>No items found.</p>
  </div>
</section>