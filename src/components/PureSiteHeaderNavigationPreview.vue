<template>
  <div
    class="pure-site-header-navigation-preview"
    :class="{
      'pure-site-header-navigation-preview--mobile': mobileMenu,
      'pure-site-header-navigation-preview--sticky': sticky,
      'pure-site-header-navigation-preview--hide-on-scroll': hideOnScroll
    }"
  >
    <span
      v-if="showSiteTitle"
      class="pure-site-header-navigation-preview__title"
    >
      {{ title }}
    </span>

    <div
      v-if="sticky"
      class="pure-site-header-navigation-preview__indicators"
    >
      <span class="pure-preview__indicator">
        Sticky
      </span>

      <span
        v-if="hideOnScroll"
        class="pure-preview__indicator"
      >
        Hide on scroll
      </span>
    </div>

    <button
      v-if="mobileMenu && hasMenuItems"
      class="pure-site-header-navigation-preview__toggle"
      type="button"
      aria-label="Mobile menu enabled"
      tabindex="-1"
    >
      <span class="pure-site-header-navigation-preview__toggle-line"></span>
      <span class="pure-site-header-navigation-preview__toggle-line"></span>
    </button>

    <nav
      v-if="hasMenuItems"
      class="pure-site-header-navigation-preview__pages"
    >
      <span
        v-for="page in visibleListedPages"
        :key="`page-${page.id}`"
      >
        {{ page.title }}
      </span>

      <span
        v-for="(item, index) in customLinks"
        :key="`custom-${index}`"
      >
        {{ item.label}}
      </span>
    </nav>

    <span
      v-if="!showSiteTitle && !hasMenuItems"
      class="pure-site-header-navigation-preview__empty"
    >
      Navigation is empty
    </span>
  </div>
</template>

<script>
export default {
  data() {
    return {
      title: "Site title",
      listedPages: []
    };
  },

  computed: {
    showSiteTitle() {
      return this.toBool(
        this.content.showsitetitle,
        true
      );
    },

    showListedPages() {
      return this.toBool(
        this.content.showlistedpages,
        true
      );
    },

    mobileMenu() {
      return this.toBool(
        this.content.mobilemenu,
        true
      );
    },

    sticky() {
      return this.toBool(
        this.content.sticky,
        false
      );
    },

    hideOnScroll() {
      return this.sticky && this.toBool(
        this.content.hideonscroll,
        false
      );
    },

    customLinks() {
      const links = Array.isArray(this.content.customlinks)
        ? this.content.customlinks
        : [];

      return links.filter(item =>
        item.label?.trim() &&
        (
          item.link ||
          item.anchor?.trim()
        )
      );
    },

    visibleListedPages() {
      return this.showListedPages
        ? this.listedPages
        : [];
    },

    hasMenuItems() {
      return (
        this.visibleListedPages.length > 0 ||
        this.customLinks.length > 0
      );
    }
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
    }
  },

  async mounted() {
    try {
      const response = await this.$api.get(
        "navigation-preview"
      );

      this.title = response.title || "Site title";

      this.listedPages = Array.isArray(response.pages)
        ? response.pages
        : [];
    } catch (error) {
      console.error(
        "Could not load navigation preview:",
        error
      );
    }
  }
};
</script>