<template>
  <k-block-figure
    class="k-block-type-pure-text"
    :is-empty="!content.text"
    empty-icon="text"
    empty-text="Enter text …"
    @open="open"
    @update="update"
  >
    <div class="pure-text-preview__indicators">
      <span class="pure-preview__indicator">SIZE {{ selectedFontSize.toUpperCase() }}</span>
    </div>
    <div
      class="pure-text-preview"
      :style="textStyle"
      v-html="content.text"
    />
  </k-block-figure>
</template>

<script>
export default {
  props: {
    content: Object,
  },

  data() {
    return {
      siteSizes: { mobile: 16, tablet: 16, desktop: 16 },
      editorWidth: 0,
    };
  },

  mounted() {
    this.loadSiteSizes();
    this.$nextTick(() => {
      const field = this.$el.closest('.k-blocks-field, .k-field[data-type="blocks"], .k-field');
      this.previewContainer = field?.querySelector('.k-blocks-list, .k-blocks-empty, .k-blocks') || field || this.$el;
      this.widthObserver = new ResizeObserver(() => {
        this.editorWidth = this.previewContainer.getBoundingClientRect().width;
      });
      this.widthObserver.observe(this.previewContainer);
      this.editorWidth = this.previewContainer.getBoundingClientRect().width;
    });
  },

  beforeDestroy() {
    this.widthObserver?.disconnect();
  },

  computed: {
    selectedFontSize() {
      const size = String(this.content?.fontsize || 's').toLowerCase();
      return ['xxs', 'xs', 's', 'sm', 'm', 'ml', 'l', 'xl', 'xxl', '3xl', '4xl'].includes(size) ? size : 's';
    },

    textStyle() {
      const style = {
        fontSize: this.previewFontSize,
      };

      const fontFamily = this.cleanFontFamily(this.content?.fontfamily);
      const lineHeight = this.cleanDimension(this.content?.lineheight, 'line-height');
      const letterSpacing = this.cleanDimension(this.content?.letterspacing, 'letter-spacing');
      const textAlignments = ['left', 'center', 'right', 'justify'];
      const textAlign = this.content?.textalign || 'left';

      style.fontFamily = fontFamily || '"TeX Gyre Heros", helvetica, arial, sans-serif';
      style.textAlign = textAlignments.includes(textAlign) ? textAlign : 'left';

      if (lineHeight) {
        style.lineHeight = lineHeight;
      }

      if (letterSpacing) {
        style.letterSpacing = letterSpacing;
      }

      return style;
    },

    previewFontSize() {
      // Match Pure frontend's size utility classes, including mobile fallbacks.
      const desktopOrTablet = this.editorWidth >= 600;
      const sizes = {
        xxs: 1 / (1.618 * 1.618), xs: 1 / 1.309, s: 1,
        sm: desktopOrTablet ? 1.309 : 1,
        m: 1.618, ml: 1.618 * 1.309, l: 1.618 * 1.618,
        xl: desktopOrTablet ? 1.618 ** 3 : 1.618 ** 2,
        xxl: desktopOrTablet ? 1.618 ** 4 : 1.618 ** 2,
        '3xl': desktopOrTablet ? 1.618 ** 5 : 1,
        '4xl': desktopOrTablet ? 1.618 ** 6 : 1,
      };
      const base = this.editorWidth >= 600 ? this.siteSizes.tablet : this.siteSizes.mobile;
      return `${base * (sizes[this.selectedFontSize] || 1)}px`;
    },
  },

  methods: {
    async loadSiteSizes() {
      try {
        const response = await this.$api.get('pure-text-preview-settings');
        const data = response?.data || response || {};
        const mobile = Number(data.mobile) || 16;
        const tablet = Number(data.tablet) || mobile;
        this.siteSizes = { mobile, tablet, desktop: Number(data.desktop) || tablet };
      } catch (error) {
        console.warn('Pure Text: Could not load site typography settings.', error);
      }
    },
    cleanFontFamily(value) {
      return String(value || '').trim().replace(/[;{}<>]/g, '');
    },

    cleanDimension(value, type) {
      const normalized = String(value || '')
        .trim()
        .replace(/(\d)\s+(?=[a-z%])/gi, '$1');

      if (!normalized) {
        return '';
      }

      const pattern = type === 'line-height'
        ? /^(?:normal|(?:\d+(?:\.\d*)?|\.\d+)(?:px|em|rem|vw|vh|%)?)$/i
        : /^(?:normal|[-+]?(?:\d+(?:\.\d*)?|\.\d+)(?:px|em|rem|vw|vh|%)?)$/i;

      return pattern.test(normalized) ? normalized : '';
    },
  },
};
</script>
