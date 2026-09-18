<?php if ($items = $block->items()->toStructure()): ?>
    <nav aria-label="Footer navigation">
        <ul>
            <?php foreach ($items as $item): ?>
                <?php if ($item->label()->isNotEmpty()): ?>
                    <li>
                        <?php if (
                            $item->link()->isNotEmpty() ||
                            $item->anchor()->isNotEmpty()
                        ): ?>

                            <?php snippet('render/pure-links', [
                                'label'  => $item->label(),
                                'link'   => $item->link(),
                                'anchor' => $item->anchor(),
                                'newTab' => $item->newTab()->toBool(),
                            ]) ?>

                        <?php else: ?>

                            <span>
                                <?= $item->label()->esc() ?>
                            </span>

                        <?php endif ?>
                    </li>
                <?php endif ?>
            <?php endforeach ?>
        </ul>
    </nav>
<?php endif ?>