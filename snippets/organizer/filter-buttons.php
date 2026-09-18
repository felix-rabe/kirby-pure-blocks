<?php
/**
 * FILE: /site/snippets/organizer/filter-buttons.php
 */

$enabled           = isset($enabled) ? (bool)$enabled : false;
$dateEnabled       = isset($dateEnabled) ? (bool)$dateEnabled : false;
$multiSelect       = isset($multiSelect) ? (bool)$multiSelect : false;
$enableItemScaler  = isset($enableItemScaler) ? (bool)$enableItemScaler : false;
$items             = $items ?? null;
$scopeClass        = isset($scopeClass) ? (string)$scopeClass : '';
$buttonsClass      = isset($buttonsClass) ? (string)$buttonsClass : 'filter-buttons';
$itemSelector      = isset($itemSelector) ? (string)$itemSelector : '';
$labelAll          = isset($labelAll) ? (string)$labelAll : 'All';
$margin            = isset($margin) ? (string)$margin : 'none';
$organizerId       = isset($organizerId) ? (string)$organizerId : '';

// NEW: Sticky Settings
$isSticky          = isset($isSticky) ? (bool)$isSticky : false;
$stickyTop         = isset($stickyTop) ? (string)$stickyTop : 'none';

// Exit only if there is truly nothing to render
if ((!$enabled && !$dateEnabled && !$enableItemScaler) || !$items || $scopeClass === '' || $itemSelector === '') {
    return;
}

$allTags  = [];
$allYears = [];

foreach ($items as $p) {
    if ($enabled) {
        foreach (fieldTags($p, 'tags') as $t) {
            $t = trim((string)$t);
            if ($t !== '') $allTags[] = $t;
        }
    }

    if ($dateEnabled) {
        try {
            if ($p->date()->isNotEmpty()) {
                $y = trim((string)$p->date()->toDate('Y'));
                if ($y !== '') $allYears[] = $y;
            }
        } catch (\Throwable $e) {
        }
    }
}

$tags  = array_values(array_unique($allTags));
$years = array_values(array_unique($allYears));

sort($tags, SORT_NATURAL | SORT_FLAG_CASE);
rsort($years, SORT_NATURAL);

$uid = 'fb-' . substr(md5($scopeClass . $buttonsClass . $itemSelector . microtime(true)), 0, 10);
$scaleId = 'scale-' . $uid;

$filterConfig = [
    'scopeClass'   => $scopeClass,
    'itemSelector' => $itemSelector,
    'multi'        => $multiSelect,
];

// Generate dynamic classes based on settings
$wrapperClasses = classList([
    'filter-buttons',
    htmlspecialchars($buttonsClass, ENT_QUOTES, 'UTF-8'),
    'margin-bottom-' . $margin,
    $isSticky ? 'is-sticky' : '', // Triggers sticky behavior in CSS
    $isSticky && $stickyTop !== 'none' ? 'sticky-top-' . $stickyTop : ''
]);
?>

<div class="<?= $wrapperClasses ?>"
     data-filter-buttons="<?= $uid ?>"
     data-filter-config="<?= htmlspecialchars(json_encode($filterConfig), ENT_QUOTES, 'UTF-8') ?>"
     role="group"
         <?= pureOnScrollAttribute() ?>
    >

  <?php if ($enabled || $dateEnabled): ?>
    <button type="button" class="btn filter-btn filter-btn-all active" data-type="all" data-value="*" aria-pressed="true">
      <?= htmlspecialchars($labelAll, ENT_QUOTES, 'UTF-8') ?>
    </button>
  <?php endif; ?>

  <?php if ($enabled): ?>
    <?php foreach ($tags as $tag): ?>
      <?php $slug = str_replace(' ', '-', strtolower($tag)); ?>
      <button type="button"
              class="btn filter-btn"
              data-type="tag"
              data-value="<?= $slug ?>"
              data-label="<?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?>"
              aria-pressed="false">
        <?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?>
      </button>
    <?php endforeach ?>
  <?php endif ?>

  <?php if ($dateEnabled): ?>
    <?php foreach ($years as $year): ?>
      <button type="button"
              class="btn filter-btn filter-btn-year"
              data-type="year"
              data-value="<?= $year ?>"
              aria-pressed="false">
        <?= htmlspecialchars($year, ENT_QUOTES, 'UTF-8') ?>
      </button>
    <?php endforeach ?>
  <?php endif ?>

  <?php if ($enableItemScaler): ?>
    <?php snippet('organizer/scale', [
        'scaleId' => $scaleId,
        'organizerId' => $organizerId,
    ]) ?>
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const root = document.querySelector('[data-filter-buttons="<?= $uid ?>"]');
  if (!root) return;

  const cfg = JSON.parse(root.dataset.filterConfig);
  const scope = root.closest('.' + cfg.scopeClass) || document;
  const gridElement = scope.querySelector('.pure-organizer');
  if (!gridElement) return;

  const items = Array.from(gridElement.querySelectorAll(cfg.itemSelector));
  const allButton = root.querySelector('.filter-btn-all');
  const emptyMessage = scope.querySelector('.pure-organizer-filter-empty');

  let activeFilters = { tag: [], year: [] };

  function sortByVisualPosition(a, b) {
    const rectA = a.getBoundingClientRect();
    const rectB = b.getBoundingClientRect();

    const topDiff = rectA.top - rectB.top;
    if (Math.abs(topDiff) > 0.5) return topDiff;

    const leftDiff = rectA.left - rectB.left;
    if (Math.abs(leftDiff) > 0.5) return leftDiff;

    return a.compareDocumentPosition(b) & Node.DOCUMENT_POSITION_FOLLOWING ? -1 : 1;
  }

  function applyFilter(shouldAnimate = true, skipAnimation = false) {
    const visibleClass = 'visible';

    const shown = items.filter(item => {
      if (activeFilters.tag.length === 0 && activeFilters.year.length === 0) return true;

      const itemTags = (item.getAttribute('data-tags') || '')
        .split(',')
        .map(t => t.trim().toLowerCase())
        .filter(Boolean);

      const itemYear = (item.getAttribute('data-year') || '').trim();

      const matchesTag = activeFilters.tag.length === 0 ||
        activeFilters.tag.some(f =>
          item.classList.contains('tag-' + f) ||
          itemTags.includes(f.replace(/-/g, ' '))
        );

      const matchesYear = activeFilters.year.length === 0 ||
        activeFilters.year.some(f =>
          item.classList.contains('year-' + f) ||
          itemYear === f
        );

      return matchesTag && matchesYear;
    });

    const hasActiveFilter =
      activeFilters.tag.length > 0 || activeFilters.year.length > 0;

    if (emptyMessage) {
      const showEmpty = hasActiveFilter && shown.length === 0;

      if (showEmpty) {
        if (emptyMessage.hidden) {
          emptyMessage.hidden = false;
          emptyMessage.classList.remove('is-visible');
          emptyMessage.offsetHeight;

          requestAnimationFrame(() => {
            emptyMessage.classList.add('is-visible');
          });
        }
      } else {
        emptyMessage.classList.remove('is-visible');
        emptyMessage.hidden = true;
      }
    }

    items.forEach(item => {
      item.style.display = shown.includes(item) ? '' : 'none';
    });

    if (window.PureOrganizer) {
      window.PureOrganizer.relayout(gridElement);
    }

    if (window.updateFirstVisibleClass) {
      window.updateFirstVisibleClass();
    }

    if (skipAnimation) {
      shown.forEach(el => {
        el.style.transition = 'none';
        el.style.opacity = '1';
        el.style.transform = 'none';
        el.classList.add(visibleClass);
      });
      return;
    }

    if (shouldAnimate && shown.length) {
      const toAnimate = shown.slice().sort(sortByVisualPosition);

      toAnimate.forEach(el => {
        el.classList.remove(visibleClass);
        el.style.transition = 'none';
        el.style.opacity = '0';
        el.style.transform = 'translateY(16px)';
      });

      gridElement.offsetHeight;

      requestAnimationFrame(() => {
        toAnimate.forEach((el, i) => {
          el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
          el.style.opacity = '1';
          el.style.transform = 'translateY(0)';
          el.style.transitionDelay = `${Math.min(i * 100, 500)}ms`;
          el.classList.add(visibleClass);
        });

        if (window.PureOrganizer) {
          window.PureOrganizer.relayout(gridElement);
        }
      });
    }
  }

  let resizeTimer;
  window.addEventListener('resize', () => {
    items.forEach(el => {
      if (el.style.display !== 'none') {
        el.style.transition = 'none';
        el.style.opacity = '1';
        el.style.transform = 'none';
      }
    });

    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      applyFilter(false, true);
    }, 120);
  });

  root.addEventListener('click', e => {
    const btn = e.target.closest('.filter-btn[data-type]');
    if (!btn) return;

    const type = btn.dataset.type;
    const val = btn.dataset.value;

    if (type === 'all') {
      activeFilters.tag = [];
      activeFilters.year = [];
    } else {
      const idx = activeFilters[type].indexOf(val);

      if (idx > -1) {
        activeFilters[type].splice(idx, 1);
      } else {
        if (!cfg.multi) activeFilters[type] = [];
        activeFilters[type].push(val);
      }
    }

    if (allButton) {
      allButton.classList.toggle(
        'active',
        activeFilters.tag.length === 0 && activeFilters.year.length === 0
      );
    }

    root.querySelectorAll('.filter-btn[data-type]').forEach(b => {
      if (b.dataset.type !== 'all') {
        b.classList.toggle('active', activeFilters[b.dataset.type].includes(b.dataset.value));
      }
    });

    applyFilter(true, false);
  });
});
</script>