<?php if ($page->layout()->isNotEmpty()): ?>

<?php
$hasMaxLayoutWidth   = $page->maxLayoutWidthToggle()->toBool();
$maxLayoutWidthValue = $page->maxLayoutWidthValue()->isNotEmpty()
  ? $page->maxLayoutWidthValue()->int()
  : null;

$maxLayoutAlignment  = $page->maxLayoutWidthAlignment()->or('center')->value();

$layouts = $page->layout()->toLayouts();
?>

<section class="layout">

  <?php foreach ($layouts as $layout): ?>

    <?php
    $columnGap = $layout->columnGap()->or('none')->value();
    $rowGap = $layout->rowGap()->or('s')->value();
    $marginTop = $layout->marginTop()->or('s')->value();
    $marginBottom = $layout->marginBottom()->or('s')->value();
    $marginLeftRight = $layout->marginLeftRight()->or('s')->value();
    $alignitems = $layout->alignItems()->or('start')->value();
    $paddingTop = $layout->paddingTop()->or('none')->value();
    $paddingBottom = $layout->paddingBottom()->or('none')->value();
    $paddingLeftRight = $layout->paddingLeftRight()->or('none')->value();

    $hasMaxLayoutSectionWidth   = $layout->maxLayoutSectionWidthToggle()->toBool();
    $maxLayoutSectionWidthValue = $layout->maxLayoutSectionWidthValue()->isNotEmpty()
      ? $layout->maxLayoutSectionWidthValue()->int()
      : null;
    $maxLayoutSectionAlignment  = $layout->maxLayoutSectionWidthAlignment()->or('center')->value();

    $layoutSectionFullBleed = $layout->layoutSectionFullBleed()->toBool();

    $backgroundColor = $layout->backgroundColor()->isNotEmpty() ? $layout->backgroundColor()->value() : null;
    $fullBleedBg = $layout->backgroundColorFullBleed()->toBool();

    $isCover = $layout->coverToggle()->toBool();
    $coverMedia = $layout->coverBackgroundMedia()->toFile();

    $columnGapClass = "column-gap-" . $columnGap;
    $rowGapClass = "row-gap-" . $rowGap;
    $marginTopClass = "margin-top-" . $marginTop;
    $marginBottomClass = "margin-bottom-" . $marginBottom;
    $marginLeftRightClass = "margin-left-right-" . $marginLeftRight;
    $alignitemsClass = "align-items-" . $alignitems;
    $paddingTopClass = "padding-top-" . $paddingTop;
    $paddingBottomClass = "padding-bottom-" . $paddingBottom;
    $paddingLeftRightClass = "padding-left-right-" . $paddingLeftRight;

    // Determine effective width behavior per section
    $effectiveHasMaxWidth = false;
    $effectiveMaxWidthValue = null;
    $effectiveAlignment = 'center';

    if (!$layoutSectionFullBleed) {
      if ($hasMaxLayoutSectionWidth && $maxLayoutSectionWidthValue) {
        $effectiveHasMaxWidth = true;
        $effectiveMaxWidthValue = $maxLayoutSectionWidthValue;
        $effectiveAlignment = $maxLayoutSectionAlignment;
      } elseif ($hasMaxLayoutWidth && $maxLayoutWidthValue) {
        $effectiveHasMaxWidth = true;
        $effectiveMaxWidthValue = $maxLayoutWidthValue;
        $effectiveAlignment = $maxLayoutAlignment;
      }
    }

    $outerSectionClasses = array_filter([
      'layout-section-wrapper',
      $layoutSectionFullBleed ? 'full-bleed-section' : '',
      $isCover ? 'cover-layout' : '',
      ($backgroundColor && $fullBleedBg) ? '' : ($backgroundColor ? 'has-background' : ''),
      $marginTopClass,
      $marginBottomClass,
      $marginLeftRightClass,
    ]);

    $outerSectionStyle = ($backgroundColor && $fullBleedBg)
      ? 'background-color:' . $backgroundColor . ';'
      : '';

    $innerSectionClasses = array_filter([
      'layout-section-inner',
      'grid',
      $columnGapClass,
      $rowGapClass,
      $alignitemsClass,
      $paddingTopClass,
      $paddingBottomClass,
      $paddingLeftRightClass,
      ($effectiveHasMaxWidth && $effectiveMaxWidthValue) ? 'align-' . $effectiveAlignment : '',
      ($backgroundColor && !$fullBleedBg) ? 'has-background' : '',
    ]);

    $innerSectionStyle = '';

    if ($effectiveHasMaxWidth && $effectiveMaxWidthValue) {
      $innerSectionStyle = 'max-width:' . (int)$effectiveMaxWidthValue . 'px;';
      if ($backgroundColor && !$fullBleedBg) {
        $innerSectionStyle .= 'background-color:' . $backgroundColor . ';';
      }
    } elseif ($backgroundColor && !$fullBleedBg) {
      $innerSectionStyle = 'background-color:' . $backgroundColor . ';';
    }
    ?>


  <section
    class="<?= implode(' ', $outerSectionClasses) ?>"
    id="<?= esc($layout->id()) ?>"
    <?= $outerSectionStyle ? 'style="' . esc($outerSectionStyle) . '"' : '' ?>
    <?= pureOnScrollAttribute((bool)$backgroundColor) ?>
  >
      <div
        class="<?= implode(' ', $innerSectionClasses) ?>"
        <?= $innerSectionStyle ? 'style="' . esc($innerSectionStyle) . '"' : '' ?>
      >

        <?php if ($isCover && $coverMedia): ?>
          <?php if ($coverMedia->type() === 'video'): ?>
            <video
              class="cover-video"
              src="<?= esc($coverMedia->url()) ?>"
              autoplay muted loop playsinline
              <?= pureOnScrollAttribute() ?>
            ></video>
          <?php else: ?>
            <?php
            $imageFile = $coverMedia;
            $alt       = $coverMedia->alt();
            $focus     = $coverMedia->focus()?->isNotEmpty() ? $coverMedia->focus()->value() : 'center';
            $link      = null;
            $captionContent = null;
            $captionClasses = '';
            $ratio = null;
            $crop = false;
            $size = 2500;
            snippet('render/image', compact(
              'imageFile', 'alt', 'focus', 'link',
              'captionContent', 'captionClasses', 'ratio', 'crop', 'size'
            ));
            ?>
          <?php endif ?>
        <?php endif ?>

        <?php foreach ($layout->columns() as $column): ?>
          <div class="column has-<?= esc($column->span()) ?>-spans<?php if ($column->blocks()->isEmpty()): ?> empty<?php endif ?>">
            <div class="blocks <?= esc($rowGapClass) ?>">

              <?php foreach ($column->blocks() as $block): ?>
                <?php
                  $mb = $block->marginBottom()->or('none')->value();
                  $marginClass = ($mb !== 'none') ? 'margin-bottom-' . esc($mb) : '';
                  $anchorValue = ltrim(trim((string)$block->anchor()->value()), '#');
                  $blockId = $anchorValue !== '' ? $anchorValue : $block->id();
                ?>
                <div
                  id="<?= esc($blockId, 'attr') ?>"
                  class="block block-type-<?= esc($block->type()) ?> <?= $marginClass ?>"
                >
                  <?php snippet('blocks/' . $block->type(), ['block' => $block]) ?>
                </div>
              <?php endforeach ?>

            </div>
          </div>
        <?php endforeach ?>

      </div>
    </section>

  <?php endforeach ?>

</section>

<?php endif ?>
