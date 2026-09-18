<template>
  <div class="pure-site-footer-navigation-preview">
    <nav
      v-if="items.length"
      class="pure-site-footer-navigation-preview__items"
    >
      <span
        v-for="(item, index) in items"
        :key="`footer-item-${index}`"
      >
        {{ item.label }}
      </span>
    </nav>

    <span
      v-else
      class="pure-site-footer-navigation-preview__empty"
    >
      No footer items
    </span>
  </div>
</template>

<script>
export default {
  computed: {
    items() {
      const items = Array.isArray(this.content.items)
        ? this.content.items
        : [];

      return items.filter(item =>
        item.label?.trim()
      );
    }
  }
};
</script>