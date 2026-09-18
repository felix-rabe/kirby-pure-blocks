<div class="scale-controls">
  <span class="scale-label">Scale:</span>
  
  <i class="icon-grid-small">
    <span></span><span></span><span></span><span></span>
  </i>

  <input
    type="range"
    id="<?= htmlspecialchars($scaleId, ENT_QUOTES, 'UTF-8') ?>"
    class="pure-organizer-scale"
    data-organizer-scale-for="<?= htmlspecialchars((string)($organizerId ?? ''), ENT_QUOTES, 'UTF-8') ?>"
    min="0.2"
    max="1.8"
    step="0.01"
    value="1.0"
    aria-label="Scale organizer items">

  <i class="icon-grid-large">
    <span></span>
  </i>
</div>