<?php

/** @var \Kirby\Cms\Block $block */

// Optional override for contexts such as sliders/slideshows.
// Normal image blocks keep the global on-scroll transition by default.
$onScrollTransition ??= true;

$imageFile = $block->image()->toFile();

if (!$imageFile) {
    return;
}

$alt   = $block->alt()->or($imageFile->alt())->esc();
$focus = $imageFile->focus()->isNotEmpty()
    ? $imageFile->focus()->value()
    : 'center';

$linkObject = $block->linkObject()->toObject();
$linkUrl    = $linkObject->link()->toUrl();
$linkTarget = $linkObject->target()->toBool()
    ? ' target="_blank" rel="noopener"'
    : '';

$caption            = $block->caption();
$captionTextAlign   = $block->captionTextAlign()->or('left')->value();

$captionClasses = implode(' ', array_filter([
    "text-align-{$captionTextAlign}"
]));

$ratio = $block->ratio()->isNotEmpty()
    ? $block->ratio()->value()
    : null;

$crop = $block->crop()->toBool();

$corners = $block->corners()->or('square')->value();

$activateLightbox = $block->activateLightbox()->toBool();

$border = $block->border()->toBool();
$borderColor = $block->borderColor()->isNotEmpty()
    ? $block->borderColor()->value()
    : null;

snippet('render/pure-image', [
    'imageFile'        => $imageFile,
    'alt'              => $alt,
    'focus'            => $focus,
    'linkUrl'          => $linkUrl,
    'linkTarget'       => $linkTarget,
    'captionContent'   => $caption->isNotEmpty() ? $caption->toHtml() : null,
    'captionClasses'   => $captionClasses,
    'ratio'            => $ratio,
    'crop'             => $crop,
    'corners'          => $corners,
    'activateLightbox' => $activateLightbox,
    'border'           => $border,
    'borderColor'        => $borderColor,
    'onScrollTransition' => $onScrollTransition,
]);
