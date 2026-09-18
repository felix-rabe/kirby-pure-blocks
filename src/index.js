import PureSiteHeaderNavigationPreview from "./components/PureSiteHeaderNavigationPreview.vue";
import PureSiteFooterNavigationPreview from "./components/PureSiteFooterNavigationPreview.vue";
import PureSpacerPreview from "./components/PureSpacerPreview.vue"; 
import PureImagePreview from "./components/PureImagePreview.vue";
import PureFlexibleTextPreview from "./components/PureFlexibleTextPreview.vue";
import PureVideoPreview from "./components/PureVideoPreview.vue";
import PureSwiperPreview from "./components/PureSwiperPreview.vue";
import PureOrganizerPreview from "./components/PureOrganizerPreview.vue";

panel.plugin("kirbypure/blocks", {
  blocks: {
    "pure-site-header-navigation": PureSiteHeaderNavigationPreview,
    "pure-site-footer-navigation": PureSiteFooterNavigationPreview,
    "pure-spacer": PureSpacerPreview,
    "pure-image": PureImagePreview,
    "pure-flexible-text": PureFlexibleTextPreview,
    "pure-video": PureVideoPreview,
    "pure-swiper": PureSwiperPreview,
    "pure-organizer": PureOrganizerPreview
  }
});