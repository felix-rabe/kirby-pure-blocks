<template>
  <k-block-figure
    class="k-block-type-pure-organizer"
    :is-empty="!loading && !visibleItems.length"
    empty-icon="grid"
    empty-text="No entries found."
    @open="open"
    @update="update"
  >
    <div ref="preview" class="pure-organizer-preview">
      <div
        v-if="indicators.length"
        class="pure-media-preview__indicators pure-organizer-preview__indicators"
      >
        <span
          v-for="indicator in indicators"
          :key="indicator"
          class="pure-preview__indicator"
        >
          {{ indicator }}
        </span>
      </div>

      <div v-if="loading" class="pure-organizer-preview__status">
        Loading entries …
      </div>

      <div v-else-if="error" class="pure-organizer-preview__status">
        {{ error }}
      </div>

      <div
        v-else-if="visibleItems.length"
        ref="grid"
        class="pure-organizer-preview__grid"
        :class="`is-${displayMode}`"
        :style="{ gap: previewGap }"
      >
        <article
          v-for="item in visibleItems"
          :key="item.id"
          ref="item"
          class="pure-organizer-preview__item"
          :style="itemStyle(item)"
        >
          <div
            v-if="hasThumbnail(item.thumbnail)"
            class="pure-organizer-preview__media"
            :class="thumbnailClasses(item.thumbnail)"
            :style="thumbnailFrameStyle(item.thumbnail)"
          >
            <img
              v-if="thumbnailType(item.thumbnail) === 'image'"
              :src="item.thumbnail.url"
              :alt="item.thumbnail.alt || ''"
              :style="thumbnailImageStyle(item.thumbnail)"
              @load="scheduleLayout"
            >

            <video
              v-else-if="thumbnailType(item.thumbnail) === 'video' && item.thumbnail.url"
              :src="item.thumbnail.url"
              :poster="item.thumbnail.poster || null"
              muted
              autoplay
              loop
              playsinline
              preload="metadata"
              @loadedmetadata="scheduleLayout"
            />

            <img
              v-else-if="thumbnailType(item.thumbnail) === 'video' && item.thumbnail.poster"
              :src="item.thumbnail.poster"
              alt=""
              @load="scheduleLayout"
            >

            <template v-else-if="thumbnailType(item.thumbnail) === 'swiper' && firstSwiperSlide(item.thumbnail)">
              <video
                v-if="firstSwiperSlide(item.thumbnail).type === 'video'"
                :src="firstSwiperSlide(item.thumbnail).url"
                muted
                autoplay
                loop
                playsinline
                preload="metadata"
                :style="thumbnailImageStyle(item.thumbnail)"
                @loadedmetadata="scheduleLayout"
              />
              <img
                v-else
                :src="firstSwiperSlide(item.thumbnail).url"
                :alt="firstSwiperSlide(item.thumbnail).alt || ''"
                :style="thumbnailImageStyle(item.thumbnail)"
                @load="scheduleLayout"
              >
            </template>

            <div
              v-else-if="thumbnailType(item.thumbnail) === 'video' && item.thumbnail.external"
              class="pure-organizer-preview__external-video"
            >
              External video
            </div>
          </div>
          <div
            v-else
            class="pure-organizer-preview__no-thumbnail"
          >
            No thumbnail found
          </div>

        </article>
      </div>
    </div>
  </k-block-figure>
</template>

<script>
export default {
  data() {
    return {
      items: [],
      loading: true,
      error: null,
      resizeObserver: null,
      layoutFrame: null,
    };
  },

  computed: {
    sourcePage() {
      const pages = Array.isArray(this.content.sourcepage)
        ? this.content.sourcepage
        : [];

      return pages[0] || null;
    },

    sourceKey() {
      return this.sourcePage?.id || "site";
    },

    displayMode() {
      const raw = this.content.displaymode ?? this.content.displayMode ?? "grid";
      const value = typeof raw === "object"
        ? (raw.value ?? raw.id ?? raw.text ?? "grid")
        : raw;

      return String(value).toLowerCase().includes("masonry")
        ? "masonry"
        : "grid";
    },


    gap() {
      const raw = this.content.gap ?? "s";
      return typeof raw === "object"
        ? (raw.value ?? raw.id ?? raw.text ?? "s")
        : String(raw);
    },

    previewGap() {
      const gaps = {
        none: "0px",
        xxs: "0.375rem",
        xs: "0.75rem",
        s: "1rem",
        m: "1.625rem",
        l: "2.625rem",
        xl: "4.25rem",
        xxl: "4.25rem",
        "3xl": "6.875rem",
        "4xl": "11rem",
      };

      return gaps[this.gap] || gaps.s;
    },

    tagFilter() {
      return this.toBool(this.content.tagfilter, false);
    },

    dateFilter() {
      return this.toBool(this.content.datefilter, false);
    },

    filterMulti() {
      return this.toBool(this.content.filtermulti, false);
    },

    filterSticky() {
      return this.toBool(this.content.filtersticky, false);
    },

    itemScaler() {
      return this.toBool(this.content.itemscaler, false);
    },

    showTags() {
      return this.toBool(this.content.showtags, false);
    },

    showPageTitle() {
      return this.toBool(this.content.showpagetitle, false);
    },

    indicators() {
      const indicators = [this.displayMode === "masonry" ? "Masonry" : "Grid"];
      if (this.tagFilter) indicators.push("Tag Filter");
      if (this.dateFilter) indicators.push("Date Filter");
      if (this.filterMulti) indicators.push("Multi-select");
      if (this.filterSticky) indicators.push("Sticky Filter");
      if (this.itemScaler) indicators.push("Scaler");
      if (this.showTags) indicators.push("Tags");
      if (this.showPageTitle) indicators.push("Page Title");
      return indicators;
    },

    prefilteredItems() {
      let items = [...this.items];
      const tags = this.normalizeTags(this.content.filterbytags);

      if (tags.length) {
        items = items.filter((item) =>
          item.tags.some((tag) => tags.includes(tag))
        );
      }

      if (this.toBool(this.content.ordertoggle, false)) {
        items.reverse();
      }

      const limit = Number(this.content.limititems || 0);
      if (limit > 0) {
        items = items.slice(0, limit);
      }

      return items;
    },

    visibleItems() {
      return this.prefilteredItems;
    },
  },

  watch: {
    sourceKey() {
      this.loadItems();
    },

    displayMode() {
      this.scheduleLayout();
    },

    gap() {
      this.scheduleLayout();
    },

    visibleItems() {
      this.scheduleLayout();
    },
  },

  mounted() {
    this.resizeObserver = new ResizeObserver(() => this.scheduleLayout());
    if (this.$refs.preview) {
      this.resizeObserver.observe(this.$refs.preview);
    }

    this.$panel?.events?.on?.("page.sort", this.onPageChange);
    this.$panel?.events?.on?.("page.changeStatus", this.onPageChange);
    this.loadItems();
  },

  beforeDestroy() {
    this.$panel?.events?.off?.("page.sort", this.onPageChange);
    this.$panel?.events?.off?.("page.changeStatus", this.onPageChange);
    if (this.resizeObserver) this.resizeObserver.disconnect();
    if (this.layoutFrame) cancelAnimationFrame(this.layoutFrame);
  },

  methods: {
    onPageChange() {
      this.loadItems();
    },

    toBool(value, fallback = false) {
      if (value === undefined || value === null || value === "") {
        return fallback;
      }

      return value === true || value === 1 || value === "1" || value === "true";
    },

    normalizeTags(value) {
      if (Array.isArray(value)) {
        return value
          .map((tag) => typeof tag === "string" ? tag : tag?.value || tag?.text || "")
          .map((tag) => tag.trim())
          .filter(Boolean);
      }

      if (typeof value === "string") {
        return value.split(",").map((tag) => tag.trim()).filter(Boolean);
      }

      return [];
    },

    async loadItems() {
      this.loading = true;
      this.error = null;

      try {
        const source = encodeURIComponent(this.sourceKey.replaceAll("/", "+"));
        const response = await this.$api.get(`organizer-preview/${source}`);
        this.items = Array.isArray(response?.items) ? response.items : [];
      } catch (error) {
        console.error("Could not load organizer preview:", error);
        this.items = [];
        this.error = "Could not load organizer entries.";
      } finally {
        this.loading = false;
        this.scheduleLayout();
      }
    },

    itemSpan(item) {
      return Math.max(1, Math.min(12, Number(item.thumbnailWidth || 6)));
    },

    itemStyle(item) {
      if (this.displayMode === "masonry") return {};

      return {
        gridColumn: `span ${this.itemSpan(item)}`,
      };
    },

    organizerElements() {
      const grid = this.$refs.grid;
      if (!grid) return [];

      return Array.from(
        grid.querySelectorAll(":scope > .pure-organizer-preview__item")
      );
    },

    resetMasonry() {
      const grid = this.$refs.grid;
      const items = this.organizerElements();

      if (grid) grid.style.height = "";

      items.forEach((element) => {
        element.style.position = "";
        element.style.left = "";
        element.style.top = "";
        element.style.width = "";
      });
    },

    layoutMasonry() {
      const grid = this.$refs.grid;
      const elements = this.organizerElements();

      if (!grid || !elements.length) return;

      if (this.displayMode !== "masonry") {
        this.resetMasonry();
        return;
      }

      const containerWidth = grid.clientWidth;
      if (!containerWidth) return;

      const styles = getComputedStyle(grid);
      const parsedGutter = Number.parseFloat(styles.columnGap || styles.gap);
      const gutter = Number.isFinite(parsedGutter) ? parsedGutter : 12;
      const columnWidth = (containerWidth - (11 * gutter)) / 12;
      const step = columnWidth + gutter;
      const columnBottoms = new Array(12).fill(0);

      elements.forEach((element, index) => {
        const item = this.visibleItems[index];
        if (!item) return;

        const span = this.itemSpan(item);
        const width = (columnWidth * span) + (gutter * Math.max(0, span - 1));

        element.style.position = "absolute";
        element.style.width = `${width}px`;
        element.style.left = "0";
        element.style.top = "0";

        const height = element.getBoundingClientRect().height;
        let bestColumn = 0;
        let bestY = Infinity;

        for (let start = 0; start <= 12 - span; start++) {
          const y = Math.max(...columnBottoms.slice(start, start + span));
          if (y < bestY) {
            bestY = y;
            bestColumn = start;
          }
        }

        const x = bestColumn * step;
        const y = Number.isFinite(bestY) ? bestY : 0;
        const bottom = y + height + gutter;

        element.style.left = `${x}px`;
        element.style.top = `${y}px`;

        for (let column = bestColumn; column < bestColumn + span; column++) {
          columnBottoms[column] = bottom;
        }
      });

      const height = Math.max(0, ...columnBottoms) - gutter;
      grid.style.height = `${Math.max(0, height)}px`;
    },

    scheduleLayout() {
      this.$nextTick(() => {
        if (this.layoutFrame) cancelAnimationFrame(this.layoutFrame);
        this.layoutFrame = requestAnimationFrame(() => this.layoutMasonry());
      });
    },

    thumbnailType(thumbnail) {
      if (!thumbnail) return null;
      return thumbnail.type || (thumbnail.url ? "image" : null);
    },

    firstSwiperSlide(thumbnail) {
      return Array.isArray(thumbnail?.slides)
        ? thumbnail.slides.find((slide) => slide?.url) || null
        : null;
    },

    hasThumbnail(thumbnail) {
      const type = this.thumbnailType(thumbnail);

      if (type === "image") return Boolean(thumbnail?.url);
      if (type === "video") return Boolean(thumbnail?.url || thumbnail?.poster || thumbnail?.external);
      if (type === "swiper") return Boolean(this.firstSwiperSlide(thumbnail));

      return false;
    },

    thumbnailClasses(thumbnail) {
      return {
        'is-rounded': thumbnail.corners === 'rounded',
        'is-circle': thumbnail.corners === 'circle',
        'has-border': thumbnail.border === true,
      };
    },

    thumbnailFrameStyle(thumbnail) {
      const style = {};
      const type = this.thumbnailType(thumbnail);

      if (type === 'image' && thumbnail.crop && thumbnail.ratio) {
        style.aspectRatio = String(thumbnail.ratio).replace('/', ' / ');
      }

      if (type === 'video' && Number(thumbnail.width) > 0 && Number(thumbnail.height) > 0) {
        style.aspectRatio = `${thumbnail.width} / ${thumbnail.height}`;
      }

      if (type === 'swiper' && thumbnail.aspectRatio && thumbnail.aspectRatio !== 'auto') {
        style.aspectRatio = String(thumbnail.aspectRatio).replace(':', ' / ');
      }

      if (thumbnail.border && thumbnail.borderColor) {
        style.borderColor = thumbnail.borderColor;
      }

      return style;
    },

    thumbnailImageStyle(thumbnail) {
      const type = this.thumbnailType(thumbnail);

      return {
        objectFit: type === 'swiper'
          ? (thumbnail.imageFit || 'cover')
          : (thumbnail.crop ? 'cover' : 'contain'),
      };
    },
  },
};
</script>
