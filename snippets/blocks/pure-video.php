<?php

/** @var \Kirby\Cms\Block $block */

// Basic fields
$caption = $block->caption();
$width   = $block->width();
$height  = $block->height();

// Defaults passed to the renderer
$url             = null;
$posterUrl       = null;
$thumbhashSvgUri = null;

// Link
$linkObject = $block->linkObject()->toObject();
$linkUrl    = $linkObject->link()->toUrl();
$linkTarget = $linkObject->target()->toBool()
    ? ' target="_blank" rel="noopener"'
    : '';

// Corners
$corners = $block->corners()->or('square')->value();

// Kirby-hosted video
if ($block->location()->value() === 'kirby' && $video = $block->video()->toFile()) {
    $url = $video->url();

    if ($posterImage = $block->poster()->toFile()) {
        try {
            $posterUrl = $posterImage->thumb([
                'width'   => 1280,
                'sharpen' => 25,
                'format'  => 'webp',
                'quality' => 75,
            ])->url();
        } catch (Throwable $e) {
            $posterUrl = $posterImage->url();
        }

        if ($posterImage->thumbhash()) {
            $thumbhashSvgUri = $posterImage->thumbhashUri([
                'blur' => 2,
            ]);
        }
    }
} else {
    // External URL
    $url = $block->url()->value();

    if ($posterImage = $block->poster()->toFile()) {
        $posterUrl = $posterImage->url();

        if ($posterImage->thumbhash()) {
            $thumbhashSvgUri = $posterImage->thumbhashUri([
                'blur' => 2,
            ]);
        }
    }
}

// Caption
$captionTextAlign = $block->captionTextAlign()->or('left')->value();

$captionClasses = implode(' ', array_filter([
    "text-align-{$captionTextAlign}",
]));

// Playback behavior
$autoplay    = $block->autoplay()->toBool();
$controls    = $block->controls()->toBool();
$loop        = $block->loop()->toBool();
$muted       = $block->muted()->toBool() || $autoplay;
$playsinline = $autoplay;

// Dimensions
$videoWidth  = $width->isNotEmpty() ? (float)$width->value() : null;
$videoHeight = $height->isNotEmpty() ? (float)$height->value() : null;

snippet('render/pure-video', [
    'url'                => $url,
    'posterUrl'          => $posterUrl,
    'captionContent'     => $caption->isNotEmpty() ? $caption->toHtml() : null,
    'captionClasses'     => $captionClasses,
    'corners'            => $corners,
    'linkUrl'            => $linkUrl,
    'linkTarget'         => $linkTarget,
    'width'              => $videoWidth,
    'height'             => $videoHeight,
    'autoplay'           => $autoplay,
    'controls'           => $controls,
    'loop'               => $loop,
    'muted'              => $muted,
    'playsinline'        => $playsinline,
    'preload'            => 'none',
    'thumbhashSvgUri'    => $thumbhashSvgUri,
    'onScrollTransition' => true,
]);