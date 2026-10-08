<?php

/** @var \Kirby\Cms\Block $block */

$link = $block->link();
$text = trim($block->text()->value());

if ($link->isEmpty() || $text === '') {
    return;
}

$fontSize = $block->fontSize()->or('s')->value();
$fontSizes = ['xxs', 'xs', 's', 'sm', 'm', 'ml', 'l', 'xl', 'xxl', '3xl', '4xl'];
$fontSize = in_array($fontSize, $fontSizes, true) ? $fontSize : 's';

$url = $link->toUrl();

if ($url === '') {
    return;
}
?>

<div class="pure-button-reveal" <?= pureOnScrollAttribute() ?>>
    <a
        class="btn font-size-<?= esc($fontSize, 'attr') ?>"
        href="<?= esc($url, 'attr') ?>"
    >
        <?= esc($text) ?>
    </a>
</div>
