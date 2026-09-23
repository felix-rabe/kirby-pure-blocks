<?php
$imageFile ??= null;
$alt ??= $imageFile?->alt() ?? '';
$focus ??= $imageFile?->focus()?->isNotEmpty() ? $imageFile->focus()->value() : 'center';
$linkUrl ??= null;
$linkTarget ??= '';
$size ??= 2400;
$quality ??= null; // Optional per-render override for generated raster quality

$captionContent ??= null;
$captionClasses ??= '';

$ratio ??= null;
$crop ??= false;

// --- Corners ---
$corners ??= 'square';

// --- Lightbox ---
$activateLightbox ??= false;

// --- Scroll transition ---
$onScrollTransition ??= true;


// --- Border ---
$border ??= false;
$borderColor ??= null;

// Lightbox group (for multiple galleries on the same page)
$lightboxGroup ??= 'gallery';

if ($imageFile):
    $extension = $imageFile->extension();
    $isGif = $extension === 'gif';
    $isSvg = $extension === 'svg';

    // Raster processing (for WebP/JPEG thumbs) applies to non-GIF and non-SVG images.
    $canGenerateThumbs = !$isGif && !$isSvg;

    // Original URL fallback
    $src = $imageFile->url();

    // Default image dimensions
    $width = $imageFile->width();
    $height = $imageFile->height();

    // Prepare thumbnail sources
    $dataSrc = $src;
    $webpSrc = null;
    $thumbhash = null;
    $thumbhashSvgUri = null;

    // Define responsive sizes depending on site toggle
    $sizes = site()->generateThumbs()->toBool()
    ? [400, 600, 800, 1000, 1200, 1520, 2000, 2400, 3040]
    : [$size]; // fast single-size fallback
    $webpSrcset = '';
    $jpegSrcset = '';

    // Thumbhash placeholder
    if (!$isSvg) {
        $thumbhash = $imageFile->thumbhash();
        if ($thumbhash) {
            $thumbhashSvgUri = $imageFile->thumbhashUri(['blur' => 2]);
        }
    }

    // Generate thumbnails for raster images (JPG/PNG)
    if ($canGenerateThumbs) {
        $thumbHeight = null;

        // Apply ratio if set
        if ($ratio) {
            [$wRatio, $hRatio] = explode('/', $ratio);
        }

        try {
            // Generate WebP srcset
            foreach ($sizes as $thumbWidth) {
                if ($ratio) {
                    $thumbHeight = intval($thumbWidth * $hRatio / $wRatio);
                }
                $webpThumb = $imageFile->thumb([
                    'width' => $thumbWidth,
                    'height' => $thumbHeight,
                    'sharpen' => 25,
                    'crop' => $crop,
                    'format' => 'webp',
                    'quality' => $quality ?? 75
                ]);
                $webpSrcset .= $webpThumb->url() . ' ' . $webpThumb->width() . 'w, ';
            }
            $webpSrc = trim($webpSrcset, ', ');

            // Generate JPEG srcset and fallback
            foreach ($sizes as $thumbWidth) {
                if ($ratio) {
                    $thumbHeight = intval($thumbWidth * $hRatio / $wRatio);
                }
                $jpegThumb = $imageFile->thumb([
                    'width' => $thumbWidth,
                    'height' => $thumbHeight,
                    'sharpen' => 25,
                    'crop' => $crop,
                    'format' => 'jpeg',
                    'quality' => $quality ?? 80
                ]);
                $jpegSrcset .= $jpegThumb->url() . ' ' . $jpegThumb->width() . 'w, ';
            }
            $dataSrc = trim($jpegSrcset, ', ');
            $width = $imageFile->width();
            $height = $imageFile->height();
        } catch (Throwable $e) {
            // fallback to original
            $dataSrc = $imageFile->url();
            $webpSrc = null;
        }
    }

    // --- Aspect-ratio for container / placeholder ---
    if ($ratio) {
        [$wRatio, $hRatio] = explode('/', $ratio);
        $ratioDecimal = round($wRatio / $hRatio, 4);
    } elseif ($width && $height) {
        $ratioDecimal = round($width / $height, 4);
    } else {
        $ratioDecimal = 1; // fallback square
    }
    $aspectRatioStyle = "aspect-ratio: {$ratioDecimal};";

    // Image orientation classes
    $isLandscape = $imageFile->isLandscape();
    $isPortrait = $imageFile->isPortrait();
    $isSquare = $imageFile->isSquare();

    // Placeholder src
    $initialSrc = $thumbhashSvgUri ?: 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';

    // Raster image class
    $imgClass = implode(' ', array_filter([
        !$isSvg ? 'is-raster-image' : '',
        $corners === 'rounded' ? 'corners-rounded' : ($corners === 'circle' ? 'corners-circle' : '')
    ]));

    // --- Wrapper classes ---
    $wrapperClasses = implode(' ', array_filter([
        'pure-image',
        $isLandscape ? 'is-landscape' : ($isPortrait ? 'is-portrait' : ($isSquare ? 'is-square' : ''))
    ]));

    // --- Lightbox source (respects crop) ---
    $lightboxSrc = $imageFile->url();
    if ($activateLightbox && $crop && $canGenerateThumbs) {
        try {
            $lightboxThumb = $imageFile->thumb([
                'width' => 2500,
                'height' => $ratio ? intval(2500 * $hRatio / $wRatio) : null,
                'crop' => true,
            ]);
            $lightboxSrc = $lightboxThumb->url();
        } catch (Throwable $e) {
            // fallback to original
            $lightboxSrc = $imageFile->url();
        }
    }

    // --- Border class ---
    $borderClass = $border ? 'has-border' : '';
    $figureClassAddon = trim($borderClass);

    // Put border color into a CSS variable (only if set)
    $borderStyle = '';
    if ($border && $borderColor) {
        $borderStyle = '--image-border-color: ' . htmlspecialchars($borderColor) . ';';
    }

?>

<!-- Lightbox / Desktop -->
<?php if ($activateLightbox): ?>
<figure class="<?= trim($wrapperClasses . ' ' . $figureClassAddon) ?> pure-image-desktop" style="object-position: <?= $focus ?>; <?= $aspectRatioStyle ?> <?= $borderStyle ?>" <?= pureOnScrollAttribute($onScrollTransition) ?>>
    <a href="<?= htmlspecialchars($lightboxSrc) ?>" class="lightbox pure-image-desktop" data-group="<?= htmlspecialchars($lightboxGroup) ?>"
       <?= $captionContent ? ' data-parvus-caption="' . htmlspecialchars(strip_tags($captionContent)) . '"' : '' ?>>
        <?php if ($canGenerateThumbs) : ?>
            <picture>
                <?php if ($webpSrc) : ?>
                    <source type="image/webp" data-srcset="<?= $webpSrc ?>" sizes="(min-width: 1200px) 1200px, (min-width: 800px) 800px, 100vw" />
                <?php endif; ?>
                <img class="<?= $imgClass ?>" src="<?= htmlspecialchars($initialSrc) ?>"
                     data-src="<?= htmlspecialchars($dataSrc) ?>" alt="<?= htmlspecialchars($alt) ?>"
                     width="<?= $width ?>" height="<?= $height ?>" style="object-position: <?= $focus ?>;" loading="lazy" />
            </picture>
        <?php elseif ($isGif || $isSvg) : ?>
            <img class="<?= $imgClass ?>" src="<?= htmlspecialchars($initialSrc) ?>"
                 data-src="<?= htmlspecialchars($dataSrc) ?>" alt="<?= htmlspecialchars($alt) ?>"
                 width="<?= $width ?>" height="<?= $height ?>" style="object-position: <?= $focus ?>;" loading="lazy" />
        <?php endif; ?>
    </a>

    <?php if ($captionContent) : ?>
        <figcaption class="<?= htmlspecialchars($captionClasses) ?>">
            <?= $captionContent ?>
        </figcaption>
    <?php endif; ?>
</figure>

<!-- Mobile / Lightbox -->
<figure class="<?= trim($wrapperClasses . ' ' . $figureClassAddon) ?> pure-image-mobile" style="object-position: <?= $focus ?>; <?= $aspectRatioStyle ?> <?= $borderStyle ?>" <?= pureOnScrollAttribute($onScrollTransition) ?>>
    <?php if ($canGenerateThumbs) : ?>
        <picture>
            <?php if ($webpSrc) : ?>
                <source type="image/webp" data-srcset="<?= $webpSrc ?>" sizes="(min-width: 1200px) 1200px, (min-width: 800px) 800px, 100vw" />
            <?php endif; ?>
            <img class="<?= $imgClass ?>" src="<?= htmlspecialchars($initialSrc) ?>"
                 data-src="<?= htmlspecialchars($dataSrc) ?>" alt="<?= htmlspecialchars($alt) ?>"
                 width="<?= $width ?>" height="<?= $height ?>" style="object-position: <?= $focus ?>;" loading="lazy" />
        </picture>
    <?php elseif ($isGif || $isSvg) : ?>
        <img class="<?= $imgClass ?>" src="<?= htmlspecialchars($initialSrc) ?>"
             data-src="<?= htmlspecialchars($dataSrc) ?>" alt="<?= htmlspecialchars($alt) ?>"
             width="<?= $width ?>" height="<?= $height ?>" style="object-position: <?= $focus ?>;" loading="lazy" />
    <?php endif; ?>

    <?php if ($captionContent) : ?>
        <figcaption class="<?= htmlspecialchars($captionClasses) ?>">
            <?= $captionContent ?>
        </figcaption>
    <?php endif; ?>
</figure>

<?php else: // Unified image with link ?>
<figure class="<?= trim($wrapperClasses . ' ' . $figureClassAddon) ?>" style="object-position: <?= $focus ?>; <?= $aspectRatioStyle ?> <?= $borderStyle ?>" <?= pureOnScrollAttribute($onScrollTransition) ?>>
    <?php
    $linkOpen = '';
    $linkClose = '';

    if (!$activateLightbox && $linkUrl) {
        $linkOpen = '<a href="' . htmlspecialchars($linkUrl) . '"' . $linkTarget . '>';
        $linkClose = '</a>';
    }
    ?>

    <?= $linkOpen ?>
        <?php if ($canGenerateThumbs) : ?>
            <picture>
                <?php if ($webpSrc) : ?>
                    <source type="image/webp" data-srcset="<?= $webpSrc ?>" sizes="(min-width: 1200px) 1200px, (min-width: 800px) 800px, 100vw" />
                <?php endif; ?>
                <img class="<?= $imgClass ?>" src="<?= htmlspecialchars($initialSrc) ?>"
                     data-src="<?= htmlspecialchars($dataSrc) ?>" alt="<?= htmlspecialchars($alt) ?>"
                     width="<?= $width ?>" height="<?= $height ?>" style="object-position: <?= $focus ?>;" loading="lazy" />
            </picture>
        <?php elseif ($isGif || $isSvg) : ?>
            <img class="<?= $imgClass ?>" src="<?= htmlspecialchars($initialSrc) ?>"
                 data-src="<?= htmlspecialchars($dataSrc) ?>" alt="<?= htmlspecialchars($alt) ?>"
                 width="<?= $width ?>" height="<?= $height ?>" style="object-position: <?= $focus ?>;" loading="lazy" />
        <?php endif; ?>
    <?= $linkClose ?>

    <?php if ($captionContent) : ?>
        <figcaption class="<?= htmlspecialchars($captionClasses) ?>">
            <?= $captionContent ?>
        </figcaption>
    <?php endif; ?>
</figure>
<?php endif; ?>
<?php endif; ?>