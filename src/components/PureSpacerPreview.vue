<template>
  <div
    class="pure-spacer-preview"
    :style="{ height: previewHeight }"
  >
    <span class="pure-spacer-preview__value">
      {{ size }}{{ unit }}
    </span>
  </div>
</template>

<script>
export default {
  props: {
    content: Object
  },

  computed: {
    size() {
      return Number(this.content?.size) || 100;
    },

    unit() {
      return ["px", "vw"].includes(this.content?.unit) ? this.content.unit : "px";
    },

    previewHeight() {
      return `${Math.max(this.size, 1)}${this.unit}`;
    }
  }
};
</script>
