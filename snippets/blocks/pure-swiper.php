<?php

/** @var \Kirby\Cms\Block $block */

$files = $block->images()->toFiles();

if ($files->isEmpty()) {
    return;
}

// Swiper settings
$effect        = $block->effect()->or('slide')->value();
$loop          = $block->loop()->toBool();
$rewind        = $block->rewind()->toBool();
$draggable     = $block->draggable()->toBool();
$slidesPerView = $block->slidesPerView()->or(1)->value();

// Controls
$pagination          = $block->pagination()->toBool();
$paginationPlacement = $block->paginationPlacement()->or('inside')->value();
$navigation          = $block->navigation()->toBool();
$scrollbar           = $block->scrollbar()->toBool();

// Autoplay
$autoplay                     = $block->autoplay()->toBool();
$autoplayDelay                = $block->autoplayDelay()->or(3000)->toInt();
$autoplayDisableOnInteraction = $block->autoplayDisableOnInteraction()->toBool();
$autoplayPauseOnMouseEnter    = $block->autoplayPauseOnMouseEnter()->toBool();

// Ratio and fitting
$aspectRatio = $block->aspectRatio()->or('auto')->value();
$autoHeight  = $aspectRatio === 'auto';

$imageFit = $block->imageFit()->or('cover')->value();
$objectFit = $autoHeight ? 'contain' : $imageFit;

// Zero is exposed as a valid editor value but rendered as 1ms so Swiper can
// reliably finish each transition while the change remains visually immediate.
$speedValue       = $block->speed()->value();
$speed            = is_numeric($speedValue) ? max(1, (int)$speedValue) : 1000;
$spaceBetween     = $block->spaceBetween()->or(0)->value();
$enableMousewheel = $block->enableMousewheel()->toBool();

// Caption for the complete Swiper (not individual slides)
$caption          = $block->caption();
$captionTextAlign = $block->captionTextAlign()->or('left')->value();
$captionClasses   = implode(' ', array_filter([
    'pure-swiper-caption',
    "text-align-{$captionTextAlign}",
]));

$swiperId = 'swiper-' . uniqid();

$bool = static fn (bool $value): string => $value ? 'true' : 'false';
?>

<div class="pure-swiper"
    <?= pureOnScrollAttribute() ?>
    >

  <div
    class="swiper"
    id="<?= esc($swiperId, 'attr') ?>"
    data-effect="<?= esc($effect, 'attr') ?>"
    data-loop="<?= $bool($loop) ?>"
    data-rewind="<?= $bool($rewind) ?>"
    data-draggable="<?= $bool($draggable) ?>"
    data-slides-per-view="<?= esc((string)$slidesPerView, 'attr') ?>"
    data-pagination="<?= $bool($pagination) ?>"
    data-pagination-placement="<?= esc($paginationPlacement, 'attr') ?>"
    data-navigation="<?= $bool($navigation) ?>"
    data-scrollbar="<?= $bool($scrollbar) ?>"
    data-autoplay="<?= $bool($autoplay) ?>"
    data-autoplay-delay="<?= $autoplayDelay ?>"
    data-autoplay-disable-on-interaction="<?= $bool($autoplayDisableOnInteraction) ?>"
    data-autoplay-pause-on-mouse-enter="<?= $bool($autoplayPauseOnMouseEnter) ?>"
    data-aspect-ratio="<?= esc($aspectRatio, 'attr') ?>"
    data-object-fit="<?= esc($objectFit, 'attr') ?>"
    data-auto-height="<?= $bool($autoHeight) ?>"
    data-speed="<?= $speed ?>"
    data-space-between="<?= esc((string)$spaceBetween, 'attr') ?>"
    data-enable-mousewheel="<?= $bool($enableMousewheel) ?>"
  >

    <div class="swiper-wrapper">

      <?php foreach ($files as $file): ?>
        <div class="swiper-slide">

          <?php if ($file->type() === 'image'): ?>

            <?php
            $focus = $file->focus()->isNotEmpty()
                ? $file->focus()->value()
                : 'center';

            $ratio = $aspectRatio !== 'auto'
                ? $aspectRatio
                : null;

            snippet('render/pure-image', [
                'imageFile'          => $file,
                'alt'                => $file->alt()->esc(),
                'focus'              => $focus,
                'linkUrl'            => $file->link()->isNotEmpty()
                    ? $file->link()->toUrl()
                    : null,
                'linkTarget'         => '',
                'captionContent'     => null,
                'captionClasses'     => '',
                'ratio'              => $ratio,
                'crop'               => $objectFit === 'cover',
                'corners'            => 'square',
                'activateLightbox'   => false,
                'border'             => false,
                'borderColor'        => null,
                'onScrollTransition' => false,
            ]);
            ?>

          <?php elseif ($file->type() === 'video'): ?>

            <?php
            $posterFile      = null;
            $posterUrl       = null;
            $thumbhashSvgUri = null;

            if ($file->poster()->isNotEmpty()) {
                $posterFile = $file->poster()->toFile();
            }

            if ($posterFile) {
                try {
                    $posterUrl = $posterFile->thumb([
                        'width'   => 1280,
                        'sharpen' => 25,
                        'format'  => 'webp',
                        'quality' => 75,
                    ])->url();
                } catch (Throwable $e) {
                    $posterUrl = $posterFile->url();
                }

                if ($posterFile->thumbhash()) {
                    $thumbhashSvgUri = $posterFile->thumbhashUri([
                        'blur' => 2,
                    ]);
                }
            }

            $captionContent = $file->caption()->isNotEmpty()
                ? $file->caption()->toHtml()
                : null;

            $videoWidth = $file->width()->isNotEmpty()
                ? (float)$file->width()->value()
                : null;

            $videoHeight = $file->height()->isNotEmpty()
                ? (float)$file->height()->value()
                : null;

            snippet('render/pure-video', [
                'url'                => $file->url(),
                'posterUrl'          => $posterUrl,
                'captionContent'     => $captionContent,
                'captionClasses'     => 'video-caption',
                'corners'            => 'square',
                'linkUrl'            => null,
                'linkTarget'         => '',
                'width'              => $videoWidth,
                'height'             => $videoHeight,
                'autoplay'           => true,
                'controls'           => false,
                'loop'               => true,
                'muted'              => true,
                'playsinline'        => true,
                'preload'            => 'none',
                'thumbhashSvgUri'    => $thumbhashSvgUri,
                'onScrollTransition' => false,
            ]);
            ?>

          <?php endif ?>

        </div>
      <?php endforeach ?>

    </div>

    <?php if ($pagination && $paginationPlacement === 'inside'): ?>
      <div class="swiper-pagination"></div>
    <?php endif ?>

    <?php if ($navigation): ?>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    <?php endif ?>

    <?php if ($scrollbar): ?>
      <div class="swiper-scrollbar"></div>
    <?php endif ?>

  </div>

  <?php if ($pagination && $paginationPlacement === 'outside'): ?>
    <div class="swiper-pagination swiper-pagination-<?= esc($swiperId, 'attr') ?>-outside"></div>
  <?php endif ?>

  <?php if ($caption->isNotEmpty()): ?>
    <div class="<?= esc($captionClasses, 'attr') ?>">
      <?= $caption->toHtml() ?>
    </div>
  <?php endif ?>

</div>
