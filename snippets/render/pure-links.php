<?php

/**
 * Reusable link renderer.
 */

$label  ??= null;
$link   ??= null;
$anchor ??= null;
$newTab ??= false;
$class  ??= '';

$linkValue = $link instanceof \Kirby\Content\Field
    ? $link->value()
    : (string)($link ?? '');

$anchorValue = $anchor instanceof \Kirby\Content\Field
    ? $anchor->value()
    : (string)($anchor ?? '');

$labelValue = $label instanceof \Kirby\Content\Field
    ? $label->value()
    : (string)($label ?? '');

// Resolve Kirby link fields.
$url = $link instanceof \Kirby\Content\Field && $link->isNotEmpty()
    ? $link->toUrl()
    : $linkValue;

// Normalize anchor.
$anchorValue = ltrim(trim((string)$anchorValue), '#');

if ($anchorValue !== '') {
    $url .= '#' . $anchorValue;
}

// Nothing to render.
if ($url === '' || $labelValue === '') {
    return;
}

?>

<a
    href="<?= esc($url, 'attr') ?>"
    <?= $class !== '' ? 'class="' . esc($class, 'attr') . '"' : '' ?>
    <?= $newTab ? 'target="_blank" rel="noopener"' : '' ?>
>
    <?= esc($labelValue) ?>
</a>