<?php

$showSiteTitle   = $block->showSiteTitle()->toBool();
$showListedPages = $block->showListedPages()->toBool();
$mobileMenu      = $block->mobileMenu()->toBool();
$sticky          = $block->sticky()->toBool();
$hideOnScroll    = $sticky && $block->hideOnScroll()->toBool();

$listedPages = $showListedPages
    ? $site->children()->listed()
    : null;

$customLinks = $block->customLinks()->toStructure();

$hasMenuItems =
    ($listedPages !== null && $listedPages->isNotEmpty()) ||
    $customLinks->isNotEmpty();

$navigationId = 'pure-navigation-menu-' . $block->id();

$navigationClasses = ['pure-navigation'];

if ($mobileMenu) {
    $navigationClasses[] = 'pure-navigation--mobile';
}

if ($sticky) {
    $navigationClasses[] = 'pure-navigation--sticky';
}

if ($hideOnScroll) {
    $navigationClasses[] = 'pure-navigation--hide-on-scroll';
}

?>

<nav
    class="<?= esc(implode(' ', $navigationClasses), 'attr') ?>"
    aria-label="Main navigation"
>
    <?php if ($showSiteTitle): ?>
        <a
            class="pure-navigation__title"
            href="<?= $site->url() ?>"
        >
            <?= $site->title()->esc() ?>
        </a>
    <?php endif ?>

    <?php if ($hasMenuItems): ?>

        <?php if ($mobileMenu): ?>
            <button
                class="pure-navigation__toggle"
                type="button"
                aria-expanded="false"
                aria-controls="<?= esc($navigationId, 'attr') ?>"
                aria-label="Open menu"
            >
                <span class="pure-navigation__toggle-line"></span>
                <span class="pure-navigation__toggle-line"></span>
            </button>
        <?php endif ?>

        <div
            id="<?= esc($navigationId, 'attr') ?>"
            class="pure-navigation__menu"
        >
            <ul class="pure-navigation__pages">

                <?php if ($listedPages !== null): ?>
                    <?php foreach ($listedPages as $item): ?>
                        <li>
                            <a
                                href="<?= $item->url() ?>"
                                <?= $item->isActive() ? 'class="active" aria-current="page"' : '' ?>
                            >
                                <?= $item->title()->esc() ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                <?php endif ?>

                <?php foreach ($customLinks as $item): ?>
                    <?php if (
                        $item->label()->isNotEmpty() &&
                        (
                            $item->link()->isNotEmpty() ||
                            $item->anchor()->isNotEmpty()
                        )
                    ): ?>
                        <li>
                            <?php snippet('render/pure-links', [
                                'label'  => $item->label(),
                                'link'   => $item->link(),
                                'anchor' => $item->anchor(),
                                'newTab' => $item->newTab()->toBool(),
                            ]) ?>
                        </li>
                    <?php endif ?>
                <?php endforeach ?>

            </ul>
        </div>

    <?php endif ?>
</nav>
