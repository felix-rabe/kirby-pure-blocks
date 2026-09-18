function updateHeaderHeight() {
  const header = document.getElementById('site-header');
  const root = document.documentElement;

  if (!header) {
    root.style.setProperty('--header-height', '0px');
    return;
  }

  const style = getComputedStyle(header);
  const isVisible = style.display !== 'none' && style.visibility !== 'hidden';

  const height = isVisible
    ? Math.ceil(header.getBoundingClientRect().height)
    : 0;

  root.style.setProperty('--header-height', `${height}px`);
}

function scheduleHeaderHeightUpdate() {
  requestAnimationFrame(() => {
    updateHeaderHeight();

    // nochmal nach Layout/Fonts/images
    requestAnimationFrame(updateHeaderHeight);
  });
}

scheduleHeaderHeightUpdate();

window.addEventListener('load', scheduleHeaderHeightUpdate);
window.addEventListener('resize', scheduleHeaderHeightUpdate);

const header = document.getElementById('site-header');

if (header) {
  new ResizeObserver(scheduleHeaderHeightUpdate).observe(header);
}

if (document.fonts) {
  document.fonts.ready.then(scheduleHeaderHeightUpdate);
}