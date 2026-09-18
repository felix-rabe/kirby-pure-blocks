<?php

use Kirby\Toolkit\Str;

/**
 * Reusable video renderer.
 *
 * Expected variables:
 *
 * @var string|null $url
 * @var string|null $posterUrl
 * @var string|null $captionContent
 * @var string      $captionClasses
 * @var string      $corners
 * @var string|null $linkUrl
 * @var string      $linkTarget
 * @var int|float|null $width
 * @var int|float|null $height
 * @var bool        $autoplay
 * @var bool        $controls
 * @var bool        $loop
 * @var bool        $muted
 * @var bool        $playsinline
 * @var string      $preload
 * @var string|null $thumbhashSvgUri
 * @var bool        $onScrollTransition
 */

$url                ??= null;
$posterUrl          ??= null;
$captionContent     ??= null;
$captionClasses     ??= '';
$corners            ??= 'square';
$linkUrl            ??= null;
$linkTarget         ??= '';
$width              ??= null;
$height             ??= null;
$autoplay           ??= false;
$controls           ??= true;
$loop               ??= false;
$muted              ??= false;
$playsinline        ??= true;
$preload            ??= 'none';
$thumbhashSvgUri    ??= null;
$onScrollTransition ??= true;


// Autoplay videos must be muted for reliable browser support.
if ($autoplay) {
    $muted = true;
}

$cornerClass = match ($corners) {
    'rounded' => 'corners-rounded',
    'circle'  => 'corners-circle',
    default   => '',
};

$aspectRatioStyle = '';
$paddingTop       = '56.25%';

if (
    is_numeric($width) &&
    is_numeric($height) &&
    (float)$width > 0 &&
    (float)$height > 0
) {
    $aspectRatioStyle = 'aspect-ratio:' . (float)$width . '/' . (float)$height . ';';
    $paddingTop       = number_format(((float)$height / (float)$width) * 100, 2) . '%';
}

// Detect supported external video URLs.
$isExternal = false;
$iframeSrc  = null;
$thumbnail  = null;

$consentButtonText       = 'Load External Content';
$consentButtonBackground = '';
$consentButtonColor      = '';
$consentButtonBorder     = '';

if (is_string($url) && Str::startsWith($url, 'http')) {
    if (Str::contains($url, 'youtube.com') || Str::contains($url, 'youtu.be')) {
        if (preg_match('/(?:youtu\.be\/|v=)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1] ?? null;

            if ($videoId) {
                $iframeSrc               = 'https://www.youtube-nocookie.com/embed/' . $videoId . '?rel=0&modestbranding=1';
                $thumbnail               = 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg';
                $isExternal              = true;
                $consentButtonText       = 'Load YouTube';
                $consentButtonBackground = '#ff0033';
                $consentButtonColor      = '#ffffff';
                $consentButtonBorder     = 'none';
            }
        }
    } elseif (Str::contains($url, 'vimeo.com')) {
        if (preg_match('/vimeo\.com\/(?:video\/)?([0-9]+)/', $url, $matches)) {
            $videoId = $matches[1] ?? null;

            if ($videoId) {
                $iframeSrc               = 'https://player.vimeo.com/video/' . $videoId;
                $isExternal              = true;
                $consentButtonText       = 'Load Vimeo';
                $consentButtonBackground = '#17d5ff';
                $consentButtonColor      = '#000000';
                $consentButtonBorder     = 'none';
            }
        }
    }
}

if (!$url) {
    return;
}

$linkOpen  = '';
$linkClose = '';

if ($linkUrl && !$isExternal) {
    $linkOpen = '<a href="' . htmlspecialchars($linkUrl, ENT_QUOTES, 'UTF-8') . '"' . $linkTarget . '>';
    $linkClose = '</a>';
}

$figureClasses = implode(' ', array_filter([
    'pure-video',
    $cornerClass,
]));
?>

<figure
  class="<?= htmlspecialchars($figureClasses, ENT_QUOTES, 'UTF-8') ?>"
  <?= pureOnScrollAttribute($onScrollTransition) ?>
>
  <?php if ($isExternal && $iframeSrc): ?>
    <?php $consentPoster = $posterUrl ?: $thumbnail ?: ''; ?>

    <div
      class="pure-video__wrapper"
      style="position:relative;width:100%;padding-top:<?= htmlspecialchars($paddingTop, ENT_QUOTES, 'UTF-8') ?>;"
    >
      <div
        class="pure-video__consent"
        style="
          position:absolute;
          inset:0;
          <?= $consentPoster
              ? "background:url('" . htmlspecialchars($consentPoster, ENT_QUOTES, 'UTF-8') . "') center/cover no-repeat;"
              : '' ?>
        "
      >
        <button
          class="pure-video__consent-button"
          type="button"
          data-src="<?= htmlspecialchars($iframeSrc, ENT_QUOTES, 'UTF-8') ?>"
          style="
            position:absolute;
            top:50%;
            left:50%;
            transform:translate(-50%,-50%);
            <?= $consentButtonBackground ? 'background-color:' . $consentButtonBackground . ';' : '' ?>
            <?= $consentButtonColor ? 'color:' . $consentButtonColor . ';' : '' ?>
            <?= $consentButtonBorder ? 'border:' . $consentButtonBorder . ';' : '' ?>
          "
        >
          <?= htmlspecialchars($consentButtonText, ENT_QUOTES, 'UTF-8') ?>
        </button>
      </div>
    </div>

  <?php else: ?>
    <?= $linkOpen ?>

    <video
      class="pure-video__media lazy <?= htmlspecialchars($cornerClass, ENT_QUOTES, 'UTF-8') ?>"
      data-src="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"
      <?php if ($posterUrl): ?>
        data-poster="<?= htmlspecialchars($posterUrl, ENT_QUOTES, 'UTF-8') ?>"
      <?php endif ?>
      <?= $autoplay ? 'autoplay' : '' ?>
      <?= $controls ? 'controls' : '' ?>
      <?= $loop ? 'loop' : '' ?>
      <?= $muted ? 'muted' : '' ?>
      <?= $playsinline ? 'playsinline' : '' ?>
      preload="<?= htmlspecialchars($preload, ENT_QUOTES, 'UTF-8') ?>"
      <?= $aspectRatioStyle ? 'style="' . htmlspecialchars($aspectRatioStyle, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
    >
      <source
        data-src="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"
        type="video/mp4"
      >
      Sorry, your browser doesn't support embedded videos.
    </video>

    <?= $linkClose ?>
  <?php endif ?>

  <?php if ($captionContent): ?>
    <figcaption class="<?= htmlspecialchars($captionClasses, ENT_QUOTES, 'UTF-8') ?>">
      <?= $captionContent ?>
    </figcaption>
  <?php endif ?>

  <?php if ($thumbhashSvgUri): ?>
    <img
      src="<?= htmlspecialchars($thumbhashSvgUri, ENT_QUOTES, 'UTF-8') ?>"
      class="pseudo-thumbhash <?= htmlspecialchars($cornerClass, ENT_QUOTES, 'UTF-8') ?>"
      alt=""
    >
  <?php endif ?>
</figure>