<template>
  <k-block-figure
    class="k-block-type-pure-image"
    :is-empty="!image.url"
    empty-icon="image"
    empty-text="Select an image …"
    @open="open"
    @update="update"
  >
    <figure class="pure-image-preview">
      <div
        v-if="lightbox"
        class="pure-media-preview__indicators"
      >
        <span class="pure-preview__indicator">
          Lightbox
        </span>
      </div>

      <div
        class="pure-image-preview__frame"
        :class="frameClasses"
        :style="frameStyle"
      >
        <img
          v-if="image.url"
          :src="previewUrl || image.url"
          :alt="content.alt || ''"
          :style="imageStyle"
        >
      </div>

      <figcaption
        v-if="content.caption"
        class="pure-image-preview__caption"
        :style="captionStyle"
        v-html="content.caption"
      />
    </figure>
  </k-block-figure>
</template>

<script>
export default {
  data() {
    return {
      sitePrimaryColor: null,
      previewUrl: null,
    };
  },

  created() {
    this.loadSitePrimaryColor();
    this.loadPreviewImage();
  },

  watch: {
    'image.link': {
      handler() {
        this.loadPreviewImage();
      },
    },
    'content.crop': {
      handler() {
        this.loadPreviewImage();
      },
    },
    'content.ratio': {
      handler() {
        this.loadPreviewImage();
      },
    },
  },

  computed: {
    image() {
      return this.content.image?.[0] || {};
    },

    lightbox() {
      return this.toBool(this.content.activatelightbox, false);
    },

    frameClasses() {
      return {
        'is-rounded': this.content.corners === 'rounded',
        'is-circle': this.content.corners === 'circle',
        'has-border': this.content.border === true,
      };
    },

    frameStyle() {
      const style = {};
      const ratio = this.ratioValue(this.content.ratio);

      if (this.content.crop === true && ratio) {
        style.aspectRatio = ratio;
      }

      if (this.content.border === true) {
        style.borderColor =
          this.colorValue(this.content.bordercolor) ||
          this.sitePrimaryColor ||
          'var(--color-border)';
      }

      return style;
    },

    imageStyle() {
      return {
        objectFit: this.content.crop === true ? 'cover' : 'contain',
        // Cropped previews are already physically cropped by Kirby.
        // Keep them centered instead of applying the focus a second time.
        objectPosition: this.content.crop === true ? 'center' : null,
      };
    },

    captionStyle() {
      const align = this.content.captiontextalign || 'left';

      return {
        textAlign: ['left', 'center', 'right'].includes(align) ? align : 'left',
      };
    },
  },

  methods: {
    toBool(value, fallback = false) {
      if (
        value === undefined ||
        value === null ||
        value === ""
      ) {
        return fallback;
      }

      return (
        value === true ||
        value === 1 ||
        value === "1" ||
        value === "true"
      );
    },

    async loadPreviewImage() {
      const link = this.image?.link;

      this.previewUrl = null;

      if (!link || this.content.crop !== true || !this.content.ratio) {
        return;
      }

      try {
        // Resolve the Kirby file ID from the file API representation first.
        const fileResponse = await this.$api.get(link);
        const file = fileResponse?.data || fileResponse || {};
        const id = file.id;

        if (!id) {
          return;
        }

        // Let Kirby generate exactly the same 2400px WebP crop that the
        // frontend renderer generates. Kirby's thumb crop applies the file's
        // saved focus point itself.
        const response = await this.$api.get('image-preview', {
          id,
          ratio: this.content.ratio,
        });

        const data = response?.data || response || {};
        this.previewUrl = data.url || null;
      } catch (error) {
        console.warn('Pure Image: Could not load cropped preview.', error);
        this.previewUrl = null;
      }
    },

    async loadSitePrimaryColor() {
      try {
        const response = await this.$api.get('site');
        const site = response?.data || response || {};
        const content = site.content || {};

        this.sitePrimaryColor = this.colorValue(
          content.primarycolor ??
          content.primaryColor ??
          site.primarycolor ??
          site.primaryColor ??
          null
        );
      } catch (error) {
        console.warn('Pure Image: Could not load site primary color.', error);
        this.sitePrimaryColor = null;
      }
    },

    ratioValue(ratio) {
      if (!ratio || typeof ratio !== 'string') {
        return null;
      }

      const [width, height] = ratio.split('/').map(Number);

      if (!width || !height) {
        return null;
      }

      return `${width} / ${height}`;
    },

    colorValue(color) {
      if (!color) {
        return null;
      }

      if (typeof color === 'string') {
        return color;
      }

      return color.value || color.hex || color.color || null;
    },
  },
};
</script>
