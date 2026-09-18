<?php

/** @var \Kirby\Cms\Block $block */

$text = $block->text();

if ($text->isEmpty()) {
    return;
}

$fontSize = $block->fontSize()->or('s')->value();
$fontSizes = ['xxs', 'xs', 's', 'sm', 'm', 'ml', 'l', 'xl', 'xxl', '3xl', '4xl'];
$fontSize = in_array($fontSize, $fontSizes, true) ? $fontSize : 's';

$textAlign = $block->textAlign()->or('left')->value();
$textAlignments = ['left', 'center', 'right', 'justify'];
$textAlign = in_array($textAlign, $textAlignments, true) ? $textAlign : 'left';

$normalizeDimension = static function (string $value): string {
    $value = trim($value);
    return preg_replace('/(?<=\d)\s+(?=[a-z%])/i', '', $value) ?? $value;
};

$fontFamily = trim($block->fontFamily()->value());
$fontFamily = preg_replace('/[;{}<>]/', '', $fontFamily) ?? '';

$lineHeight = $normalizeDimension($block->lineHeight()->value());
if ($lineHeight !== '' && preg_match('/^(?:normal|(?:\d+(?:\.\d*)?|\.\d+)(?:px|em|rem|vw|vh|%)?)$/i', $lineHeight) !== 1) {
    $lineHeight = '';
}

$letterSpacing = $normalizeDimension($block->letterSpacing()->value());
if ($letterSpacing !== '' && preg_match('/^(?:normal|[-+]?(?:\d+(?:\.\d*)?|\.\d+)(?:px|em|rem|vw|vh|%)?)$/i', $letterSpacing) !== 1) {
    $letterSpacing = '';
}

$styles = [];

if ($fontFamily !== '') {
    $styles[] = 'font-family: ' . $fontFamily;
}

if ($lineHeight !== '') {
    $styles[] = 'line-height: ' . $lineHeight;
}

if ($letterSpacing !== '') {
    $styles[] = 'letter-spacing: ' . $letterSpacing;
}

$styleAttribute = $styles !== []
    ? ' style="' . esc(implode('; ', $styles), 'attr') . '"'
    : '';
?>

<div
    class="pure-flexible-text font-size-<?= esc($fontSize, 'attr') ?> text-align-<?= esc($textAlign, 'attr') ?>"
    <?= pureOnScrollAttribute() ?><?= $styleAttribute ?>
>
    <?= $text->toHtml() ?>
</div>
