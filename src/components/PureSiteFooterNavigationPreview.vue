<template>
  <div class="pure-site-footer-navigation-preview">
    <nav
      v-if="columns.length"
      class="pure-site-footer-navigation-preview__columns"
      :style="previewStyle"
    >
      <div
        v-for="(column, columnIndex) in columns"
        :key="`footer-column-${columnIndex}`"
        class="pure-site-footer-navigation-preview__column"
      >
        <strong
          v-if="column.title && column.title.trim()"
          class="pure-site-footer-navigation-preview__title"
        >
          {{ column.title }}
        </strong>

        <div class="pure-site-footer-navigation-preview__items">
          <span
            v-for="(item, itemIndex) in visibleItems(column.items)"
            :key="`footer-item-${columnIndex}-${itemIndex}`"
          >
            {{ item.label }}
          </span>
        </div>
      </div>
    </nav>

    <span
      v-else
      class="pure-site-footer-navigation-preview__empty"
    >
      No footer columns
    </span>
  </div>
</template>

<script>
export default {
  computed: {
    direction() {
      const value = this.content.direction;
      return ["row", "column"].includes(value) ? value : "row";
    },
    gap() {
      const value = this.content.gap || "s";
      return ["none", "xxs", "xs", "s", "m", "l", "xl", "xxl", "3xl", "4xl"].includes(value)
        ? value
        : "s";
    },
    previewStyle() {
      const gaps = {
        none: "0px",
        xxs: "0.375rem",
        xs: "0.75rem",
        s: "1rem",
        m: "1.625rem",
        l: "2.625rem",
        xl: "4.236rem",
        xxl: "6.854rem",
        "3xl": "11.09rem",
        "4xl": "17.944rem"
      };

      return {
        flexDirection: this.direction,
        gap: gaps[this.gap] || gaps.s
      };
    },
    columns() {
      const columns = Array.isArray(this.content.columns)
        ? this.content.columns
        : [];

      if (columns.length) {
        return columns;
      }

      // Keep existing blocks readable in the preview until they are converted.
      const legacyItems = Array.isArray(this.content.items)
        ? this.content.items
        : [];

      return legacyItems.length
        ? [{ title: "", items: legacyItems }]
        : [];
    }
  },
  methods: {
    visibleItems(items) {
      return (Array.isArray(items) ? items : []).filter(item =>
        item.label?.trim()
      );
    }
  }
};
</script>
