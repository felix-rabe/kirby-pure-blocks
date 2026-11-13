import "./layoutPreview.css";
import PureLayoutFieldPreview from "./layoutPreview.js";
import PureSiteHeaderNavigationPreview from "./components/PureSiteHeaderNavigationPreview.vue";
import PureSiteFooterNavigationPreview from "./components/PureSiteFooterNavigationPreview.vue";
import PureSpacerPreview from "./components/PureSpacerPreview.vue"; 
import PureImagePreview from "./components/PureImagePreview.vue";
import PureTextPreview from "./components/PureTextPreview.vue";
import PureVideoPreview from "./components/PureVideoPreview.vue";
import PureSwiperPreview from "./components/PureSwiperPreview.vue";
import PureOrganizerPreview from "./components/PureOrganizerPreview.vue";
import PureButtonPreview from "./components/PureButtonPreview.vue";

panel.plugin("kirbypure/blocks", {
  components: {
    "k-layout-field": PureLayoutFieldPreview
  },
  blocks: {
    "pure-site-header-navigation": PureSiteHeaderNavigationPreview,
    "pure-site-footer-navigation": PureSiteFooterNavigationPreview,
    "pure-spacer": PureSpacerPreview,
    "pure-image": PureImagePreview,
    "pure-text": PureTextPreview,
    "pure-video": PureVideoPreview,
    "pure-swiper": PureSwiperPreview,
    "pure-organizer": PureOrganizerPreview,
    "pure-button": PureButtonPreview
  }
});
import { initPureBlocksResponsiveIndicator } from "./panelResponsiveIndicator.js";
initPureBlocksResponsiveIndicator();
