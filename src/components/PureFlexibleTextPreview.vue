<template>
  <k-block-figure
    class="k-block-type-pure-flexible-text"
    :is-empty="!content.text"
    empty-icon="text"
    empty-text="Enter text …"
    @open="open"
    @update="update"
  >
    <div
      class="pure-flexible-text-preview"
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

  computed: {
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
  },

  methods: {
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
