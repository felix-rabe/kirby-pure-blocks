<?php

$size = $block->size()->or(100)->toInt();
$unit = $block->unit()->or('px')->value();
$unit = in_array($unit, ['px', 'vw'], true) ? $unit : 'px';

?>

<div
    class="pure-spacer"
    style="height: <?= $size ?><?= $unit ?>"
    aria-hidden="true"
></div>
