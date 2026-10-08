<?php
/**
 * FILE: /site/snippets/organizer/grid.php
 * Renders one Organizer layout surface. Used once normally, or once per Group.
 */
$items             = $items ?? [];
$organizerId       = (string)($grid['organizerId'] ?? uniqid('organizer-', true));
$layoutMode        = (string)($grid['layoutMode'] ?? 'grid');
$widthMode         = (string)($grid['widthMode'] ?? 'span');
$showTags          = (bool)($grid['showTags'] ?? false);
$showGroups        = (bool)($grid['showGroups'] ?? false);
$showPageTitle     = (bool)($grid['showPageTitle'] ?? false);
$showMeta          = $showTags || $showGroups || $showPageTitle;
$rowGapValue       = (string)($grid['rowGapValue'] ?? '0px');
$columnGapValue    = (string)($grid['columnGapValue'] ?? '0px');
$groupKey          = (string)($groupKey ?? 'all');
?>
<div class="pure-organizer"
     style="--organizer-row-gap: <?= htmlspecialchars($rowGapValue, ENT_QUOTES, 'UTF-8') ?>; --organizer-column-gap: <?= htmlspecialchars($columnGapValue, ENT_QUOTES, 'UTF-8') ?>;"
     data-layout="<?= htmlspecialchars($layoutMode, ENT_QUOTES, 'UTF-8') ?>"
     data-width-mode="<?= htmlspecialchars($widthMode, ENT_QUOTES, 'UTF-8') ?>"
     data-organizer-id="<?= htmlspecialchars($organizerId, ENT_QUOTES, 'UTF-8') ?>"
     data-organizer-group="<?= htmlspecialchars($groupKey, ENT_QUOTES, 'UTF-8') ?>">

  <?php foreach ($items as $subpage):
      $thumbnailWidth = $subpage->thumbnailWidth()->or(3)->toInt();
      $widthClass = 'has-' . $thumbnailWidth . '-spans';

      $tags = fieldTags($subpage, 'tags');
      $groups = fieldTags($subpage, 'groups');
      $tagClasses = array_map(fn($t) => 'tag-' . str_replace(' ', '-', strtolower(trim($t))), $tags);
      $groupClasses = array_map(fn($g) => 'group-' . str_replace(' ', '-', strtolower(trim($g))), $groups);

      $year = $subpage->date()->isNotEmpty() ? $subpage->date()->toDate('Y') : '';
      $yearClass = $year ? 'year-' . $year : '';

      $childItemClasses = classList([
          'pure-organizer-item',
          $widthClass,
          implode(' ', $tagClasses),
          implode(' ', $groupClasses),
          $yearClass
      ]);

      $thumbnailBlocks = $subpage->thumbnail()->isNotEmpty()
          ? $subpage->thumbnail()->toBlocks()
          : null;

      $firstThumbnailBlock = $thumbnailBlocks ? $thumbnailBlocks->first() : null;
      $firstThumbnailType  = $firstThumbnailBlock ? $firstThumbnailBlock->type() : null;
      $mediaThumbnailTypes = ['pure-image', 'pure-video', 'pure-swiper', 'image', 'video'];
      $metaInFlow = ($showTags || $showGroups)
          && $firstThumbnailType !== null
          && !in_array($firstThumbnailType, $mediaThumbnailTypes, true);

      $innerWrapperClasses = classList([
          'item-inner-wrapper',
          'text-block',
          $showMeta ? 'show-meta' : '',
          $metaInFlow ? 'tags-in-flow' : '',
      ]);
  ?>
    <div class="<?= $childItemClasses ?>"
         <?= pureOnScrollAttribute() ?>
         data-tags="<?= htmlspecialchars(implode(',', $tags), ENT_QUOTES, 'UTF-8') ?>"
         data-groups="<?= htmlspecialchars(implode(',', $groups), ENT_QUOTES, 'UTF-8') ?>"
         data-year="<?= htmlspecialchars((string)$year, ENT_QUOTES, 'UTF-8') ?>"
         data-span="<?= $thumbnailWidth ?>">

      <div class="<?= $innerWrapperClasses ?>">
        <?php if ($metaInFlow): ?>
          <div class="meta-wrapper meta-wrapper--flow">
            <div class="meta-infos-wrapper">
              <?php if (($showTags && !empty($tags)) || ($showGroups && !empty($groups))): ?>
                <div class="meta-tags tags--free">
                  <?php if ($showTags): ?>
                    <?php foreach ($tags as $tag): ?>
                      <div class="btn mini-btn tag"><?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endforeach ?>
                  <?php endif ?>
                  <?php if ($showGroups): ?>
                    <?php foreach ($groups as $group): ?>
                      <div class="btn mini-btn group"><?= htmlspecialchars($group, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endforeach ?>
                  <?php endif ?>
                </div>
              <?php endif ?>

              <?php if ($year): ?>
                <div class="meta-year"><div class="btn mini-btn year"><?= htmlspecialchars($year, ENT_QUOTES, 'UTF-8') ?></div></div>
              <?php endif ?>
            </div>
          </div>
        <?php endif ?>

        <?php if ($thumbnailBlocks): ?>
          <?php foreach ($thumbnailBlocks as $thumbBlock): ?>
            <?php snippet('pure-blocks-wrapper', [
                'block' => $thumbBlock,
                'extraClasses' => ['thumbnail-block']
            ]) ?>
          <?php endforeach ?>
        <?php else: ?>
          <div class="pure-organizer-no-thumbnail">No thumbnail found</div>
        <?php endif ?>

        <?php if ($showMeta && (!$metaInFlow || $showPageTitle)): ?>
          <div class="meta-wrapper">
            <?php if (($showTags || $showGroups) && !$metaInFlow): ?>
              <div class="meta-infos-wrapper">
                <?php if (($showTags && !empty($tags)) || ($showGroups && !empty($groups))): ?>
                  <div class="meta-tags tags--overlay">
                    <?php if ($showTags): ?>
                      <?php foreach ($tags as $tag): ?>
                        <div class="btn mini-btn tag"><?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?></div>
                      <?php endforeach ?>
                    <?php endif ?>
                    <?php if ($showGroups): ?>
                      <?php foreach ($groups as $group): ?>
                        <div class="btn mini-btn group"><?= htmlspecialchars($group, ENT_QUOTES, 'UTF-8') ?></div>
                      <?php endforeach ?>
                    <?php endif ?>
                  </div>
                <?php endif ?>

                <?php if ($year): ?>
                  <div class="meta-year"><div class="btn mini-btn year"><?= htmlspecialchars($year, ENT_QUOTES, 'UTF-8') ?></div></div>
                <?php endif ?>
              </div>
            <?php endif ?>

            <?php if ($showPageTitle): ?>
              <div class="meta-title"><div class="btn"><?= htmlspecialchars($subpage->title()->value(), ENT_QUOTES, 'UTF-8') ?></div></div>
            <?php endif ?>
          </div>
        <?php endif ?>
      </div>
    </div>
  <?php endforeach ?>
</div>
