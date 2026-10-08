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
 * @var string      $objectFit
 * @var string      $objectPosition
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
$objectFit          ??= 'contain';
$objectPosition     ??= 'center center';
$autoplay           ??= false;
$controls           ??= true;
$loop               ??= false;
$muted              ??= false;
$playsinline        ??= true;
$preload            ??= 'none';
$thumbhashSvgUri    ??= null;
$onScrollTransition ??= true;
$activateLightbox    ??= false;
$lightboxGroup       ??= 'gallery';


// Autoplay videos must be muted for reliable browser support.
if ($autoplay) {
    $muted = true;
}

$cornerClass = match ($corners) {
    'rounded' => 'corners-rounded',
    'circle'  => 'corners-circle',
    default   => '',
};

// Keep media fitting predictable when the configured frame differs from the source video.
$allowedObjectFits = ['contain', 'cover', 'fill', 'none', 'scale-down'];
if (!in_array($objectFit, $allowedObjectFits, true)) {
    $objectFit = 'contain';
}

$allowedObjectPositions = [
    'left top', 'center top', 'right top',
    'left center', 'center center', 'right center',
    'left bottom', 'center bottom', 'right bottom',
];
if (!in_array($objectPosition, $allowedObjectPositions, true)) {
    $objectPosition = 'center center';
}

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

if ($linkUrl && !$isExternal && !$activateLightbox) {
    $linkOpen = '<a href="' . htmlspecialchars($linkUrl, ENT_QUOTES, 'UTF-8') . '"' . $linkTarget . '>';
    $linkClose = '</a>';
}

$figureClasses = implode(' ', array_filter([
    'pure-video',
    $cornerClass,
]));

// Parvus is image-oriented. For video triggers we give it the poster as its
// initial lightbox source, then pure-parvus.js swaps that slide content for
// the actual <video>. This keeps Parvus' own trigger, overlay and transitions.
$lightboxPlaceholder = $posterUrl ?: 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

$renderVideo = function (string $extraClass = '') use (
    $url, $posterUrl, $autoplay, $controls, $loop, $muted, $playsinline,
    $preload, $aspectRatioStyle, $objectFit, $objectPosition, $cornerClass
) {
?>
    <video
      class="pure-video__media lazy <?= htmlspecialchars(trim($cornerClass . ' ' . $extraClass), ENT_QUOTES, 'UTF-8') ?>"
      data-src="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"
      <?php if ($posterUrl): ?>data-poster="<?= htmlspecialchars($posterUrl, ENT_QUOTES, 'UTF-8') ?>"<?php endif ?>
      <?= $autoplay ? 'autoplay' : '' ?>
      <?= $controls ? 'controls' : '' ?>
      <?= $loop ? 'loop' : '' ?>
      <?= $muted ? 'muted' : '' ?>
      <?= $playsinline ? 'playsinline' : '' ?>
      preload="<?= htmlspecialchars($preload, ENT_QUOTES, 'UTF-8') ?>"
      style="<?= htmlspecialchars($aspectRatioStyle . 'object-fit:' . $objectFit . ';object-position:' . $objectPosition . ';', ENT_QUOTES, 'UTF-8') ?>"
    >
      <source data-src="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" type="video/mp4">
      Sorry, your browser doesn't support embedded videos.
    </video>
<?php
};
?>

<?php if ($isExternal && $iframeSrc): ?>
<figure class="<?= htmlspecialchars($figureClasses, ENT_QUOTES, 'UTF-8') ?>" <?= pureOnScrollAttribute($onScrollTransition) ?>>
    <?php $consentPoster = $posterUrl ?: $thumbnail ?: ''; ?>
    <div class="pure-video__wrapper" style="position:relative;width:100%;padding-top:<?= htmlspecialchars($paddingTop, ENT_QUOTES, 'UTF-8') ?>;">
      <div class="pure-video__consent" style="position:absolute;inset:0;<?= $consentPoster ? "background:url('" . htmlspecialchars($consentPoster, ENT_QUOTES, 'UTF-8') . "') center/cover no-repeat;" : '' ?>">
        <button class="pure-video__consent-button" type="button" data-src="<?= htmlspecialchars($iframeSrc, ENT_QUOTES, 'UTF-8') ?>" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);<?= $consentButtonBackground ? 'background-color:' . $consentButtonBackground . ';' : '' ?><?= $consentButtonColor ? 'color:' . $consentButtonColor . ';' : '' ?><?= $consentButtonBorder ? 'border:' . $consentButtonBorder . ';' : '' ?>">
          <?= htmlspecialchars($consentButtonText, ENT_QUOTES, 'UTF-8') ?>
        </button>
      </div>
    </div>
    <?php if ($captionContent): ?><figcaption class="<?= htmlspecialchars($captionClasses, ENT_QUOTES, 'UTF-8') ?>"><?= $captionContent ?></figcaption><?php endif ?>
</figure>

<?php elseif ($activateLightbox): ?>
<!-- Lightbox / Desktop: same Parvus anchor pattern as pure-image -->
<figure class="<?= htmlspecialchars($figureClasses, ENT_QUOTES, 'UTF-8') ?> pure-video-desktop" <?= pureOnScrollAttribute($onScrollTransition) ?>>
  <a
    href="<?= htmlspecialchars($lightboxPlaceholder, ENT_QUOTES, 'UTF-8') ?>"
    class="lightbox pure-video-desktop"
    data-group="<?= htmlspecialchars($lightboxGroup, ENT_QUOTES, 'UTF-8') ?>"
    data-parvus-video="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"
    data-parvus-video-poster="<?= htmlspecialchars((string)$posterUrl, ENT_QUOTES, 'UTF-8') ?>"
    data-parvus-video-autoplay="<?= $autoplay ? '1' : '0' ?>"
    data-parvus-video-controls="<?= $controls ? '1' : '0' ?>"
    data-parvus-video-loop="<?= $loop ? '1' : '0' ?>"
    data-parvus-video-muted="<?= $muted ? '1' : '0' ?>"
    data-parvus-video-playsinline="<?= $playsinline ? '1' : '0' ?>"
    data-parvus-video-object-fit="<?= htmlspecialchars($objectFit, ENT_QUOTES, 'UTF-8') ?>"
    data-parvus-video-object-position="<?= htmlspecialchars($objectPosition, ENT_QUOTES, 'UTF-8') ?>"
    data-parvus-video-width="<?= is_numeric($width) ? (float)$width : '' ?>"
    data-parvus-video-height="<?= is_numeric($height) ? (float)$height : '' ?>"
    <?= $captionContent ? 'data-parvus-caption="' . htmlspecialchars(strip_tags($captionContent), ENT_QUOTES, 'UTF-8') . '"' : '' ?>
  >
    <!-- Parvus adds its native zoom indicator only when the trigger contains an img.
         Keep this tiny helper image in the trigger so video gets the exact same UI. -->
    <img class="pure-video__parvus-helper" src="<?= htmlspecialchars($lightboxPlaceholder, ENT_QUOTES, 'UTF-8') ?>" alt="" aria-hidden="true">
    <div class="pure-video__media-container" style="position:relative;width:100%;overflow:hidden;">
      <?php $renderVideo(); ?>
      <?php if ($thumbhashSvgUri): ?><img src="<?= htmlspecialchars($thumbhashSvgUri, ENT_QUOTES, 'UTF-8') ?>" class="pseudo-thumbhash <?= htmlspecialchars($cornerClass, ENT_QUOTES, 'UTF-8') ?>" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:<?= htmlspecialchars($objectPosition, ENT_QUOTES, 'UTF-8') ?>;" alt=""><?php endif ?>
    </div>
  </a>
  <?php if ($captionContent): ?><figcaption class="<?= htmlspecialchars($captionClasses, ENT_QUOTES, 'UTF-8') ?>"><?= $captionContent ?></figcaption><?php endif ?>
</figure>

<!-- Mobile: no lightbox, exactly like pure-image's mobile branch -->
<figure class="<?= htmlspecialchars($figureClasses, ENT_QUOTES, 'UTF-8') ?> pure-video-mobile" <?= pureOnScrollAttribute($onScrollTransition) ?>>
  <div class="pure-video__media-container" style="position:relative;width:100%;overflow:hidden;">
    <?php $renderVideo(); ?>
    <?php if ($thumbhashSvgUri): ?><img src="<?= htmlspecialchars($thumbhashSvgUri, ENT_QUOTES, 'UTF-8') ?>" class="pseudo-thumbhash <?= htmlspecialchars($cornerClass, ENT_QUOTES, 'UTF-8') ?>" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:<?= htmlspecialchars($objectPosition, ENT_QUOTES, 'UTF-8') ?>;" alt=""><?php endif ?>
  </div>
  <?php if ($captionContent): ?><figcaption class="<?= htmlspecialchars($captionClasses, ENT_QUOTES, 'UTF-8') ?>"><?= $captionContent ?></figcaption><?php endif ?>
</figure>

<?php else: ?>
<figure class="<?= htmlspecialchars($figureClasses, ENT_QUOTES, 'UTF-8') ?>" <?= pureOnScrollAttribute($onScrollTransition) ?>>
  <?= $linkOpen ?>
  <div class="pure-video__media-container" style="position:relative;width:100%;overflow:hidden;">
    <?php $renderVideo(); ?>
    <?php if ($thumbhashSvgUri): ?><img src="<?= htmlspecialchars($thumbhashSvgUri, ENT_QUOTES, 'UTF-8') ?>" class="pseudo-thumbhash <?= htmlspecialchars($cornerClass, ENT_QUOTES, 'UTF-8') ?>" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:<?= htmlspecialchars($objectPosition, ENT_QUOTES, 'UTF-8') ?>;" alt=""><?php endif ?>
  </div>
  <?= $linkClose ?>
  <?php if ($captionContent): ?><figcaption class="<?= htmlspecialchars($captionClasses, ENT_QUOTES, 'UTF-8') ?>"><?= $captionContent ?></figcaption><?php endif ?>
</figure>
<?php endif ?>