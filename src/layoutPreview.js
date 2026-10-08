import { previewSpace } from "./previewTokens.js";

function attr(attrs, name, fallback = null) {
  if (!attrs || typeof attrs !== "object") return fallback;
  if (attrs[name] !== undefined) return attrs[name];

  const wanted = name.toLowerCase();
  const key = Object.keys(attrs).find((key) => key.toLowerCase() === wanted);
  return key ? attrs[key] : fallback;
}

function bool(value) {
  return value === true || value === 1 || value === "1" || value === "true";
}

function color(value) {
  if (Array.isArray(value)) value = value[0];
  if (value && typeof value === "object") value = value.value ?? value.hex ?? null;
  return typeof value === "string" && value.trim() ? value.trim() : null;
}

function clearPreview(layout) {
  layout.classList.remove("pure-layout-preview", "pure-layout-preview--full-bleed");
  [
    "--pure-layout-preview-margin-top",
    "--pure-layout-preview-margin-bottom",
    "--pure-layout-preview-margin-sides",
    "--pure-layout-preview-padding-top",
    "--pure-layout-preview-padding-bottom",
    "--pure-layout-preview-padding-sides",
    "--pure-layout-preview-column-gap",
    "--pure-layout-preview-row-gap",
    "--pure-layout-preview-background",
    "--pure-layout-preview-max-width",
    "--pure-layout-preview-max-width-margin-left",
    "--pure-layout-preview-max-width-margin-right",
    "--pure-layout-preview-align-items",
  ].forEach((property) => layout.style.removeProperty(property));
}

function applyPreview(layout, attrs = {}) {
  clearPreview(layout);
  layout.classList.add("pure-layout-preview");

  const fullBleed = bool(attr(attrs, "layoutSectionFullBleed", false));
  const hasMaxWidth = bool(attr(attrs, "maxLayoutSectionWidthToggle", false));
  const maxWidth = Number(attr(attrs, "maxLayoutSectionWidthValue", 0));
  const maxWidthAlignment = String(attr(attrs, "maxLayoutSectionWidthAlignment", "center"));
  const background = color(attr(attrs, "backgroundColor"));
  const backgroundFullBleed = bool(attr(attrs, "backgroundColorFullBleed", false));

  layout.classList.toggle("pure-layout-preview--full-bleed", fullBleed);

  layout.style.setProperty("--pure-layout-preview-margin-top", previewSpace(attr(attrs, "marginTop", "none")));
  layout.style.setProperty("--pure-layout-preview-margin-bottom", previewSpace(attr(attrs, "marginBottom", "none")));
  layout.style.setProperty("--pure-layout-preview-margin-sides", previewSpace(attr(attrs, "marginLeftRight", "none")));
  layout.style.setProperty("--pure-layout-preview-padding-top", previewSpace(attr(attrs, "paddingTop", "none")));
  layout.style.setProperty("--pure-layout-preview-padding-bottom", previewSpace(attr(attrs, "paddingBottom", "none")));
  layout.style.setProperty("--pure-layout-preview-padding-sides", previewSpace(attr(attrs, "paddingLeftRight", "none")));
  layout.style.setProperty("--pure-layout-preview-column-gap", previewSpace(attr(attrs, "columnGap", "none")));
  layout.style.setProperty("--pure-layout-preview-row-gap", previewSpace(attr(attrs, "rowGap", "s")));
  layout.style.setProperty("--pure-layout-preview-align-items", String(attr(attrs, "alignItems", "start")));

  if (background) {
    layout.style.setProperty("--pure-layout-preview-background", background);
    layout.dataset.pureBackgroundFullBleed = backgroundFullBleed ? "true" : "false";
  } else {
    delete layout.dataset.pureBackgroundFullBleed;
  }

  if (!fullBleed && hasMaxWidth && maxWidth > 0) {
    // The Panel canvas is narrower than the frontend. Clamp the requested width
    // to the available preview width, while preserving left/center/right alignment.
    layout.style.setProperty("--pure-layout-preview-max-width", `${maxWidth}px`);

    const margins = {
      left: ["0", "auto"],
      center: ["auto", "auto"],
      right: ["auto", "0"],
    }[maxWidthAlignment] || ["auto", "auto"];

    layout.style.setProperty("--pure-layout-preview-max-width-margin-left", margins[0]);
    layout.style.setProperty("--pure-layout-preview-max-width-margin-right", margins[1]);
  }
}

export default {
  extends: "k-layout-field",

  watch: {
    value: {
      deep: true,
      handler() {
        this.$nextTick(this.applyPureLayoutPreview);
      },
    },
  },

  mounted() {
    this.$nextTick(this.applyPureLayoutPreview);
  },

  updated() {
    this.$nextTick(this.applyPureLayoutPreview);
  },

  methods: {
    applyPureLayoutPreview() {
      const layouts = Array.from(this.$el?.querySelectorAll?.(".k-layout") || []);
      const values = Array.isArray(this.value) ? this.value : [];

      layouts.forEach((layout, index) => {
        applyPreview(layout, values[index]?.attrs || {});
      });
    },
  },
};
