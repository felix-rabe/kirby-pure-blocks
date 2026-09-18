<template>
  <k-block-figure
    class="k-block-type-pure-video"
    :is-empty="isEmpty"
    empty-icon="video"
    empty-text="Select a video …"
    @open="open"
    @update="update"
  >
    <figure class="pure-video-preview">
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
        class="pure-video-preview__frame"
        :class="frameClasses"
        :style="frameStyle"
      >
        <video
        v-if="video.url"
        class="pure-video-preview__media"
        :src="video.url"
        :poster="poster.url || null"
        :autoplay="autoplay"
        :loop="loop"
        :controls="controls"
        muted
        playsinline
        preload="auto"
        />

        <img
          v-else-if="poster.url"
          class="pure-video-preview__poster"
          :src="poster.url"
          alt=""
        >

        <div
          v-else-if="isExternal"
          class="pure-video-preview__external"
        >
          <k-icon type="video" />
          <span>External video</span>
        </div>
      </div>

      <figcaption
        v-if="content.caption"
        class="pure-video-preview__caption"
        :style="captionStyle"
        v-html="content.caption"
      />
    </figure>
  </k-block-figure>
</template>

<script>
export default {
  computed: {
    video() {
      return this.content.video?.[0] || {};
    },

    poster() {
      return this.content.poster?.[0] || {};
    },

    isExternal() {
      return this.content.location === "web" && Boolean(this.content.url);
    },

    isEmpty() {
      return !this.video.url && !this.isExternal;
    },

    autoplay() {
      return this.toBool(this.content.autoplay, true);
    },

    muted() {
      return this.toBool(this.content.muted, true) || this.autoplay;
    },

    loop() {
      return this.toBool(this.content.loop, true);
    },

    controls() {
      return this.toBool(this.content.controls, false);
    },

    indicators() {
    const indicators = [];

    if (this.poster.url && !this.isExternal) {
        indicators.push("Poster");
    }

    if (this.autoplay && !this.isExternal) {
        indicators.push("Autoplay");
    }

    if (this.loop && !this.isExternal) {
        indicators.push("Loop");
    }

    if (this.controls && !this.isExternal) {
        indicators.push("Controls");
    }

    return indicators;
    },

    frameClasses() {
      return {
        "is-rounded": this.content.corners === "rounded",
        "is-circle": this.content.corners === "circle",
      };
    },

    frameStyle() {
      const width = Number(this.content.width);
      const height = Number(this.content.height);

      if (width > 0 && height > 0) {
        return {
          aspectRatio: `${width} / ${height}`,
        };
      }

      return {};
    },

    captionStyle() {
      const align = this.content.captiontextalign || "left";

      return {
        textAlign: ["left", "center", "right"].includes(align) ? align : "left",
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
  },
};
</script>
