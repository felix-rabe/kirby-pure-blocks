<template>
  <k-block-figure
    class="k-block-type-pure-button"
    :is-empty="!buttonText"
    empty-icon="url"
    empty-text="Enter button text …"
    @open="open"
    @update="update"
  >
    <div class="pure-button-preview" :style="previewVariables">
      <span class="pure-button-preview__button" :style="buttonStyle">
        {{ buttonText || 'Button' }}
      </span>
    </div>
  </k-block-figure>
</template>

<script>
export default {
  props: {
    content: Object,
  },

  data() {
    return {
      siteSettings: {},
    };
  },

  computed: {
    buttonText() {
      return String(this.content?.text || '').trim();
    },

    buttonStyle() {
      return {
        fontSize: this.previewFontSize,
      };
    },

    previewFontSize() {
      const sizes = {
        xxs: 0.472,
        xs: 0.764,
        s: 1,
        sm: 1.309,
        m: 1.618,
        ml: 2.118,
        l: 2.618,
        xl: 4.236,
        xxl: 6.854,
        '3xl': 11.09,
        '4xl': 17.944,
      };

      const size = this.content?.fontsize || 's';
      return `${sizes[size] || sizes.s}rem`;
    },

    previewVariables() {
      const primary = this.setting('colorprimary', 'colorPrimary') || '#000000';
      const secondary = this.setting('colorsecondary', 'colorSecondary') || '#ffffff';

      return {
        '--pure-preview-color-primary': primary,
        '--pure-preview-color-secondary': secondary,
        '--pure-preview-btn-color': this.resolveColor(
          this.setting('buttontextcolor', 'buttonTextColor') || 'var(--color-primary)',
          primary,
          secondary
        ),
        '--pure-preview-btn-background': this.resolveColor(
          this.setting('buttonbackgroundcolor', 'buttonBackgroundColor') || 'var(--color-secondary)',
          primary,
          secondary
        ),
        '--pure-preview-btn-border': this.resolveColor(
          this.setting('buttonbordercolor', 'buttonBorderColor') || 'var(--color-primary)',
          primary,
          secondary
        ),
        '--pure-preview-btn-hover-color': this.resolveColor(
          this.setting('buttonhovertextcolor', 'buttonHoverTextColor') || 'var(--color-secondary)',
          primary,
          secondary
        ),
        '--pure-preview-btn-hover-background': this.resolveColor(
          this.setting('buttonhoverbackgroundcolor', 'buttonHoverBackgroundColor') || 'var(--color-primary)',
          primary,
          secondary
        ),
        '--pure-preview-btn-hover-border': this.resolveColor(
          this.setting('buttonhoverbordercolor', 'buttonHoverBorderColor') || 'var(--color-primary)',
          primary,
          secondary
        ),
        '--pure-preview-btn-radius': this.buttonRadius,
        '--pure-preview-btn-padding': this.buttonPadding,
      };
    },

    buttonRadius() {
      const shape = this.setting('buttonshape', 'buttonShape') || 'pillshape';

      if (shape === 'squared') return '0';
      if (shape === 'rounded') return '0.472rem';
      return '9999px';
    },

    buttonPadding() {
      const value = this.setting('buttonpadding', 'buttonPadding') ||
        'var(--size-xs) var(--size-s) calc(var(--size-xs) + 2px) var(--size-s)';

      return String(value)
        .replaceAll('var(--size-xxs)', '0.472rem')
        .replaceAll('var(--size-xs)', '0.764rem')
        .replaceAll('var(--size-s)', '1rem')
        .replaceAll('var(--size-sm)', '1.309rem')
        .replaceAll('var(--size-m)', '1.618rem')
        .replaceAll('var(--size-ml)', '2.118rem')
        .replaceAll('var(--size-l)', '2.618rem');
    },
  },

  mounted() {
    this.loadSiteSettings();
  },

  methods: {
    async loadSiteSettings() {
      try {
        const response = await this.$api.get('site');
        const site = response?.data || response || {};
        this.siteSettings = site.content || {};
      } catch (error) {
        console.warn('Pure Button: Could not load site button settings.', error);
        this.siteSettings = {};
      }
    },

    setting(lowercase, camelcase) {
      return this.siteSettings?.[lowercase] ?? this.siteSettings?.[camelcase] ?? null;
    },

    resolveColor(value, primary, secondary) {
      const color = String(value || '').trim();
      if (color === 'var(--color-primary)') return primary;
      if (color === 'var(--color-secondary)') return secondary;
      return color;
    },
  },
};
</script>

<style>
.pure-button-preview {
  padding: 0.5rem 0;
}

.pure-button-preview__button {
  all: unset;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  text-decoration: none;
  width: auto;
  max-width: max-content;
  box-sizing: border-box;
  color: var(--pure-preview-btn-color);
  background-color: var(--pure-preview-btn-background);
  border: 1px solid var(--pure-preview-btn-border);
  border-radius: var(--pure-preview-btn-radius);
  padding: var(--pure-preview-btn-padding);
  transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
  font-family: "TeX Gyre Heros", helvetica, arial, sans-serif;
  font-weight: 400;
  letter-spacing: 0.02em;
  line-height: normal;
}

.pure-button-preview__button:hover {
  color: var(--pure-preview-btn-hover-color);
  background-color: var(--pure-preview-btn-hover-background);
  border-color: var(--pure-preview-btn-hover-border);
}
</style>
