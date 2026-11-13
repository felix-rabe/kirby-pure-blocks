// Responsive mode indicator for Kirby Blocks fields containing Pure Blocks.
// Uses the Blocks list width, not the browser viewport width.
export function initPureBlocksResponsiveIndicator() {
  if (typeof window === 'undefined' || window.__pureBlocksResponsiveIndicator) return;
  window.__pureBlocksResponsiveIndicator = true;

  const observed = new Map();
  const mode = (width) => width >= 900 ? 'Desktop (1400 px)' : width >= 600 ? 'Tablet (992 px)' : 'Mobile';

  // Find Blocks fields themselves, including the empty state (no previews yet).
  const findFields = () => {
    const fields = new Set();
    document.querySelectorAll('.k-blocks-field, .k-field[data-type="blocks"]').forEach((field) => {
      if (field.querySelector('.k-field-header')) fields.add(field);
    });
    // Support Kirby versions where the Blocks field has no dedicated type class.
    document.querySelectorAll('.k-blocks, .k-blocks-list, .k-blocks-empty').forEach((blocks) => {
      let node = blocks.parentElement;
      while (node && node !== document.body) {
        if (node.classList.contains('k-field') && node.querySelector('.k-field-header')) {
          fields.add(node);
          break;
        }
        node = node.parentElement;
      }
    });
    return fields;
  };

  const update = (field, width) => {
    const header = field.querySelector('.k-field-header');
    const label = header?.querySelector('.k-label, label') || header;
    if (!label) return;
    // Existing project-specific indicators take precedence.
    if (label.querySelector('.andre-preview-mode, .bigsun-preview-mode')) {
      label.querySelector('.pure-preview-mode')?.remove();
      return;
    }
    let badge = label.querySelector('.pure-preview-mode');
    if (!badge) {
      badge = document.createElement('span');
      badge.className = 'pure-preview-mode';
      badge.setAttribute('aria-live', 'polite');
      label.appendChild(badge);
    }
    const next = mode(width);
    // Only animate when crossing a breakpoint, never on every resize event.
    if (badge.textContent === next) return;
    const firstAppearance = !badge.textContent;
    badge.textContent = next;
    // Cancel the previous fade and start a fresh one immediately.
    // Web Animations API avoids CSS animation restart issues during resizing.
    badge._pureModeAnimation?.cancel();
    if (!firstAppearance && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      badge._pureModeAnimation = badge.animate(
        [{ opacity: 0 }, { opacity: 1 }],
        { duration: 800, easing: 'ease-out' }
      );
    }
  };

  const sync = () => {
    const fields = findFields();
    fields.forEach((field) => {
      // Measure the editor area, including when the list is empty.
      const target = field.querySelector('.k-blocks-list, .k-blocks-empty, .k-blocks') || field;
      const existing = observed.get(field);
      if (existing?.target === target) return;
      existing?.observer.disconnect();
      const observer = new ResizeObserver(([entry]) => update(field, entry.contentRect.width));
      observer.observe(target);
      observed.set(field, { target, observer });
      update(field, target.getBoundingClientRect().width);
    });
    observed.forEach(({ observer }, field) => {
      if (fields.has(field) && field.isConnected) return;
      observer.disconnect();
      field.querySelector('.pure-preview-mode')?.remove();
      observed.delete(field);
    });
  };
  const start = () => {
    new MutationObserver(sync).observe(document.body, { childList: true, subtree: true });
    sync();
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, { once: true });
  else start();
}
