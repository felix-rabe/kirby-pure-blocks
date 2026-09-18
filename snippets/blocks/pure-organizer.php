<?php
/**
 * FILE: /site/snippets/blocks/pure-organizer.php
 */

/** * HELPER FUNCTIONS
 * These ensure the block can handle tags, class lists, and file attachments
 * without crashing if global helpers are missing.
 */
if (!function_exists('classList')) {
    function classList(array $classes): string {
        return implode(' ', array_filter($classes));
    }
}

if (!function_exists('fieldTags')) {
    function fieldTags($page, string $field = 'tags'): array {
        $raw = (string) $page->{$field}();
        $parts = array_map('trim', explode(',', $raw));
        return array_values(array_filter($parts, fn($v) => $v !== ''));
    }
}

if (!function_exists('thumbnailImageFile')) {
    function thumbnailImageFile($page): ?\Kirby\Cms\File {
        if ($page->thumbnail()->isEmpty()) return null;
        foreach ($page->thumbnail()->toBlocks() as $b) {
            if ($b->type() === 'image') {
                $f = $b->content()->get('image')?->toFile();
                if ($f) return $f;
            }
        }
        return null;
    }
}

// ---------------- Determine subpages ----------------

/**
 * This closure handles the server-side collection of pages.
 * It filters by template and pre-selected tags from the Panel.
 */
$subpagesToDisplay = (function () use ($block, $site) {

    $source = $block->sourcePage()->toPage();

    $subs = ($source ? $source->children() : $site->index())
        ->listed()
        ->filter(fn ($p) => $p->intendedTemplate()->name() === 'pure-subpage');

    // Server-side tag filter (filtering the collection before it hits the browser)
    $selectedTags = array_values(array_filter(array_map('trim',
        explode(',', (string)$block->filterByTags()->value())
    )));

    if (!empty($selectedTags)) {
        $subs = $subs->filter(function ($p) use ($selectedTags) {
            $pageTags = fieldTags($p, 'tags');
            return count(array_intersect($selectedTags, $pageTags)) > 0;
        });
    }

    // Apply Sort Order (Reverse if toggled in the Panel)
    if ($block->orderToggle()->toBool()) {
        $subs = $subs->flip();
    }

    // Apply Limit Threshold
    $limit = $block->limitItems()->toInt();
    if ($limit > 0) {
        $subs = $subs->limit($limit);
    }

    return $subs;
})();

// Early exit if no pages match the criteria
if ($subpagesToDisplay->isEmpty()):
?>
<div class="pure-organizer-empty text-align-center"
    <?= pureOnScrollAttribute() ?>
    >
  <p>No entries found.</p>
</div>
<?php
return;
endif;

// ---------------- Display Mode & Settings ----------------

$displayMode          = $block->displayMode()->or('grid')->value();
$enableTagFilter      = $block->tagFilter()->toBool();
$enableFilterMulti    = $block->filterMulti()->toBool();
$enableDateFilter     = $block->dateFilter()->toBool();
$marginFilterButtons  = $block->marginBottomFilterButtons()->or('none')->value();
$filterSticky         = $block->filterSticky()->toBool();
$stickyFilterTop      = $block->stickyFilterTop()->or('none')->value();
$showTags            = $block->showTags()->toBool();
$showPageTitle       = $block->showPageTitle()->toBool();

$layoutMode = in_array($displayMode, ['grid', 'masonry'], true)
    ? $displayMode
    : 'grid';

// Configuration for Grid/Thumbnail Mode
$gridSettings = [
    'organizerId'            => $block->id(),
    'gap'                    => $block->gap()->or('s')->value(),
    'alignItems'             => $block->alignItems()->or('start')->value(),
    'justifyContent'         => $block->justifyContent()->or('flex-start')->value(),
    'enableTagFilter'        => $enableTagFilter,
    'enableFilterMulti'      => $enableFilterMulti,
    'enableDateFilter'       => $enableDateFilter,
    'layoutMode'             => $layoutMode,
    'marginFilterButtons'    => $marginFilterButtons,
    'enableItemScaler'       => $block->itemScaler()->toBool(),
    'filterSticky'           => $filterSticky,
    'stickyFilterTop'        => $stickyFilterTop,
    'showTags'               => $showTags,
    'showPageTitle'          => $showPageTitle,
];

// Route to the appropriate sub-snippet
snippet('organizer/thumbnail', [
    'subpages' => $subpagesToDisplay,
    'grid'     => $gridSettings,
]);