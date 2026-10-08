<?php
$columns = $block->columns()->toStructure();
$direction = in_array($block->direction()->value(), ['row', 'column'], true)
    ? $block->direction()->value()
    : 'row';
$gap = $block->gap()->or('s')->value();
$allowedGaps = ['none', 'xxs', 'xs', 's', 'm', 'l', 'xl', 'xxl', '3xl', '4xl'];
$gap = in_array($gap, $allowedGaps, true) ? $gap : 's';
$gapValue = $gap === 'none' ? '0' : 'var(--size-' . $gap . ')';

// Backwards compatibility for footer blocks created before columns were introduced.
if ($columns->isEmpty()) {
    $legacyItems = $block->items()->toStructure();

    if ($legacyItems->isNotEmpty()) {
        $columns = [[
            'title' => '',
            'items' => $legacyItems,
        ]];
    }
}
?>

<?php if (count($columns)): ?>
    <nav
        class="pure-site-footer-navigation"
        aria-label="Footer navigation"
        style="--pure-footer-direction: <?= esc($direction) ?>; --pure-footer-gap: <?= esc($gapValue) ?>"
    >
        <?php foreach ($columns as $column): ?>
            <?php
            $title = is_array($column) ? ($column['title'] ?? '') : $column->title()->value();
            $items = is_array($column) ? ($column['items'] ?? []) : $column->items()->toStructure();
            ?>
            <div class="pure-site-footer-navigation__column">
                <?php if (trim((string)$title) !== ''): ?>
                    <div class="pure-site-footer-navigation__title">
                        <?= esc($title) ?>
                    </div>
                <?php endif ?>

                <?php if (count($items)): ?>
                    <ul class="pure-site-footer-navigation__items">
                        <?php foreach ($items as $item): ?>
                            <?php if ($item->label()->isNotEmpty()): ?>
                                <li class="pure-site-footer-navigation__item">
                                    <?php if ($item->link()->isNotEmpty() || $item->anchor()->isNotEmpty()): ?>
                                        <?php snippet('render/pure-links', [
                                            'label'  => $item->label(),
                                            'link'   => $item->link(),
                                            'anchor' => $item->anchor(),
                                            'newTab' => $item->newTab()->toBool(),
                                        ]) ?>
                                    <?php else: ?>
                                        <span><?= $item->label()->esc() ?></span>
                                    <?php endif ?>
                                </li>
                            <?php endif ?>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </nav>
<?php endif ?>
