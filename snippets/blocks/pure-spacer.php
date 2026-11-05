<?php

$anchor = ltrim(trim((string) $block->anchor()->value()), '#');
$size = $block->size()->or(100)->toInt();
$unit = $block->unit()->or('px')->value();
$unit = in_array($unit, ['px', 'vw'], true) ? $unit : 'px';

?>

<div
    class="pure-spacer"<?php if ($anchor): ?> id="<?= esc($anchor, 'attr') ?>"<?php endif ?>
    style="height: <?= $size ?><?= $unit ?>"
    aria-hidden="true"
></div>
