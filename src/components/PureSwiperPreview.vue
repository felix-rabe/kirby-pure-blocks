<template>
  <k-block-figure
    class="k-block-type-pure-swiper"
    :is-empty="!slides.length"
    empty-icon="images"
    empty-text="Select images or videos …"
    @open="open"
    @update="update"
  >
    <figure class="pure-swiper-preview">
      <div
        v-if="indicators.length"
        class="pure-media-preview__indicators"
      >
        <span
          v-for="indicator in indicators"
          :key="indicator"
          class="pure-preview__indicator"
        >
          {{ indicator }}
        </span>
      </div>

      <div
        v-if="slides.length"
        class="pure-swiper-preview__stage"
        :style="stageStyle"
      >
        <template v-if="currentSlide">
          <video
            v-if="isVideo(currentSlide)"
            class="pure-swiper-preview__media"
            :src="currentSlide.url"
            muted
            playsinline
            preload="metadata"
          />
          <img
            v-else
            class="pure-swiper-preview__media"
            :src="currentSlide.url"
            alt=""
          >
        </template>

        <button
          v-if="slides.length > 1"
          class="pure-swiper-preview__button pure-swiper-preview__button--prev"
          type="button"
          aria-label="Previous slide"
          @click.stop="previous"
        >
          ‹
        </button>

        <button
          v-if="slides.length > 1"
          class="pure-swiper-preview__button pure-swiper-preview__button--next"
          type="button"
          aria-label="Next slide"
          @click.stop="next"
        >
          ›
        </button>

        <div
          v-if="slides.length > 1"
          class="pure-swiper-preview__pagination"
        >
          <span
            v-for="(_, index) in slides"
            :key="index"
            class="pure-swiper-preview__dot"
            :class="{ 'is-active': index === activeIndex }"
          />
        </div>
      </div>

      <figcaption
        v-if="content.caption"
        class="pure-swiper-preview__caption"
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
      activeIndex: 0,
    };
  },

  computed: {
    slides() {
      return Array.isArray(this.content.images)
        ? this.content.images.filter((file) => file?.url)
        : [];
    },

    currentSlide() {
      return this.slides[this.activeIndex] || this.slides[0] || null;
    },

    loop() {
      return this.toBool(this.content.loop, false);
    },

    rewind() {
      return this.toBool(this.content.rewind, false);
    },

    autoplay() {
      return this.toBool(this.content.autoplay, false);
    },

    pagination() {
      return this.toBool(this.content.pagination, false);
    },

    navigation() {
      return this.toBool(this.content.navigation, false);
    },

    scrollbar() {
      return this.toBool(this.content.scrollbar, false);
    },

    mousewheel() {
      return this.toBool(this.content.enablemousewheel, false);
    },

    draggable() {
        return this.toBool(this.content.draggable, false);
    },

    indicators() {
    const indicators = [];

    if (this.content.effect && this.content.effect !== "slide") {
        indicators.push(this.content.effect);
    }

    if (this.loop) indicators.push("Loop");
    if (this.rewind) indicators.push("Rewind");
    if (this.autoplay) indicators.push("Autoplay");
    if (this.pagination) indicators.push("Pagination");
    if (this.navigation) indicators.push("Navigation");
    if (this.scrollbar) indicators.push("Scrollbar");
    if (this.mousewheel) indicators.push("Mousewheel");
    if (this.draggable) indicators.push("Draggable");

    const slidesPerView = Number(this.content.slidesperview || 1);

    if (slidesPerView > 1) {
        indicators.push(`${slidesPerView} slides`);
    }

    return indicators;
    },

    captionStyle() {
      const align = this.content.captiontextalign || "left";

      return {
        textAlign: ["left", "center", "right"].includes(align) ? align : "left",
      };
    },

    stageStyle() {
      const ratio = this.content.aspectratio || "auto";

      if (ratio === "auto") {
        return {};
      }

      return {
        aspectRatio: String(ratio).replace(":", " / "),
      };
    },
  },

  watch: {
    slides() {
      if (this.activeIndex >= this.slides.length) {
        this.activeIndex = 0;
      }
    },
  },

  methods: {
    toBool(value, fallback = false) {
      if (value === undefined || value === null || value === "") {
        return fallback;
      }

      return value === true || value === 1 || value === "1" || value === "true";
    },

    isVideo(file) {
      const mime = file?.mime || "";
      const type = file?.type || "";
      const extension = file?.extension || "";

      return (
        type === "video" ||
        mime.startsWith("video/") ||
        ["mp4", "webm", "ogg", "mov", "m4v"].includes(extension.toLowerCase())
      );
    },

    previous() {
      if (!this.slides.length) return;

      this.activeIndex =
        (this.activeIndex - 1 + this.slides.length) % this.slides.length;
    },

    next() {
      if (!this.slides.length) return;

      this.activeIndex = (this.activeIndex + 1) % this.slides.length;
    },
  },
};
</script>
