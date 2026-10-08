(function(){"use strict";function o(e,t,i,s,r,d,m,u){var l=typeof e=="function"?e.options:e;return t&&(l.render=t,l.staticRenderFns=i,l._compiled=!0),{exports:e,options:l}}const f={data(){return{title:"Site title",listedPages:[]}},computed:{showSiteTitle(){return this.toBool(this.content.showsitetitle,!0)},showListedPages(){return this.toBool(this.content.showlistedpages,!0)},mobileMenu(){return this.toBool(this.content.mobilemenu,!0)},sticky(){return this.toBool(this.content.sticky,!1)},hideOnScroll(){return this.sticky&&this.toBool(this.content.hideonscroll,!1)},customLinks(){return(Array.isArray(this.content.customlinks)?this.content.customlinks:[]).filter(t=>{var i,s;return((i=t.label)==null?void 0:i.trim())&&(t.link||((s=t.anchor)==null?void 0:s.trim()))})},visibleListedPages(){return this.showListedPages?this.listedPages:[]},hasMenuItems(){return this.visibleListedPages.length>0||this.customLinks.length>0}},methods:{toBool(e,t=!1){return e==null||e===""?t:e===!0||e===1||e==="1"||e==="true"}},async mounted(){try{const e=await this.$api.get("navigation-preview");this.title=e.title||"Site title",this.listedPages=Array.isArray(e.pages)?e.pages:[]}catch(e){console.error("Could not load navigation preview:",e)}}};var y=function(){var t=this,i=t._self._c;return i("div",{staticClass:"pure-site-header-navigation-preview",class:{"pure-site-header-navigation-preview--mobile":t.mobileMenu,"pure-site-header-navigation-preview--sticky":t.sticky,"pure-site-header-navigation-preview--hide-on-scroll":t.hideOnScroll}},[t.showSiteTitle?i("span",{staticClass:"pure-site-header-navigation-preview__title"},[t._v(" "+t._s(t.title)+" ")]):t._e(),t.sticky?i("div",{staticClass:"pure-text-preview__indicators"},[i("span",{staticClass:"pure-preview__indicator"},[t._v(" Sticky ")]),t.hideOnScroll?i("span",{staticClass:"pure-preview__indicator"},[t._v(" Hide on scroll ")]):t._e()]):t._e(),t.mobileMenu&&t.hasMenuItems?i("button",{staticClass:"pure-site-header-navigation-preview__toggle",attrs:{type:"button","aria-label":"Mobile menu enabled",tabindex:"-1"}},[i("span",{staticClass:"pure-site-header-navigation-preview__toggle-line"}),i("span",{staticClass:"pure-site-header-navigation-preview__toggle-line"})]):t._e(),t.hasMenuItems?i("nav",{staticClass:"pure-site-header-navigation-preview__pages"},[t._l(t.visibleListedPages,function(s){return i("span",{key:`page-${s.id}`},[t._v(" "+t._s(s.title)+" ")])}),t._l(t.customLinks,function(s,r){return i("span",{key:`custom-${r}`},[t._v(" "+t._s(s.label)+" ")])})],2):t._e(),!t.showSiteTitle&&!t.hasMenuItems?i("span",{staticClass:"pure-site-header-navigation-preview__empty"},[t._v(" Navigation is empty ")]):t._e()])},w=[],b=o(f,y,w);const C=b.exports,x={computed:{direction(){const e=this.content.direction;return["row","column"].includes(e)?e:"row"},gap(){const e=this.content.gap||"s";return["none","xxs","xs","s","m","l","xl","xxl","3xl","4xl"].includes(e)?e:"s"},previewStyle(){const e={none:"0px",xxs:"0.375rem",xs:"0.75rem",s:"1rem",m:"1.625rem",l:"2.625rem",xl:"4.236rem",xxl:"6.854rem","3xl":"11.09rem","4xl":"17.944rem"};return{flexDirection:this.direction,gap:e[this.gap]||e.s}},columns(){const e=Array.isArray(this.content.columns)?this.content.columns:[];if(e.length)return e;const t=Array.isArray(this.content.items)?this.content.items:[];return t.length?[{title:"",items:t}]:[]}},methods:{visibleItems(e){return(Array.isArray(e)?e:[]).filter(t=>{var i;return(i=t.label)==null?void 0:i.trim()})}}};var S=function(){var t=this,i=t._self._c;return i("div",{staticClass:"pure-site-footer-navigation-preview"},[t.columns.length?i("nav",{staticClass:"pure-site-footer-navigation-preview__columns",style:t.previewStyle},t._l(t.columns,function(s,r){return i("div",{key:`footer-column-${r}`,staticClass:"pure-site-footer-navigation-preview__column"},[s.title&&s.title.trim()?i("strong",{staticClass:"pure-site-footer-navigation-preview__title"},[t._v(" "+t._s(s.title)+" ")]):t._e(),i("div",{staticClass:"pure-site-footer-navigation-preview__items"},t._l(t.visibleItems(s.items),function(d,m){return i("span",{key:`footer-item-${r}-${m}`},[t._v(" "+t._s(d.label)+" ")])}),0)])}),0):i("span",{staticClass:"pure-site-footer-navigation-preview__empty"},[t._v(" No footer columns ")])])},k=[],$=o(x,S,k);const P=$.exports,B={props:{content:Object},computed:{size(){var e;return Number((e=this.content)==null?void 0:e.size)||100},unit(){var e;return["px","vw"].includes((e=this.content)==null?void 0:e.unit)?this.content.unit:"px"},previewHeight(){return`${Math.max(this.size,1)}${this.unit}`}}};var M=function(){var t=this,i=t._self._c;return i("div",{staticClass:"pure-spacer-preview",style:{height:t.previewHeight}},[i("span",{staticClass:"pure-spacer-preview__value"},[t._v(" "+t._s(t.size)+t._s(t.unit)+" ")])])},I=[],z=o(B,M,I);const F=z.exports,A={data(){return{sitePrimaryColor:null,previewUrl:null}},created(){this.loadSitePrimaryColor(),this.loadPreviewImage()},watch:{"image.link":{handler(){this.loadPreviewImage()}},"content.crop":{handler(){this.loadPreviewImage()}},"content.ratio":{handler(){this.loadPreviewImage()}}},computed:{image(){var e;return((e=this.content.image)==null?void 0:e[0])||{}},lightbox(){return this.toBool(this.content.activatelightbox,!1)},frameClasses(){return{"has-border":this.content.border===!0,"is-rounded":this.content.corners==="rounded","is-circle":this.content.corners==="circle"}},imageClasses(){return{}},frameStyle(){const e={},t=this.ratioValue(this.content.ratio);return this.content.crop===!0&&t&&(e.aspectRatio=t),this.content.border===!0&&(e.borderColor=this.colorValue(this.content.bordercolor)||this.sitePrimaryColor||"var(--color-border)"),e},imageStyle(){return{objectFit:this.content.crop===!0?"cover":"contain",objectPosition:this.content.crop===!0?"center":null}},captionStyle(){const e=this.content.captiontextalign||"left";return{textAlign:["left","center","right"].includes(e)?e:"left"}}},methods:{toBool(e,t=!1){return e==null||e===""?t:e===!0||e===1||e==="1"||e==="true"},async loadPreviewImage(){const e=this.image&&this.image.link;if(this.previewUrl=null,!e||this.content.crop!==!0||!this.content.ratio)return;try{const t=await this.$api.get(e),i=t&&t.data||t||{},s=i.id;if(!s)return;const r=await this.$api.get("image-preview",{id:s,ratio:this.content.ratio}),d=r&&r.data||r||{};this.previewUrl=d.url||null}catch(t){console.warn("Pure Image: Could not load cropped preview.",t),this.previewUrl=null}},async loadSitePrimaryColor(){try{const e=await this.$api.get("site"),t=(e==null?void 0:e.data)||e||{},i=t.content||{};this.sitePrimaryColor=this.colorValue(i.primarycolor??i.primaryColor??t.primarycolor??t.primaryColor??null)}catch(e){console.warn("Pure Image: Could not load site primary color.",e),this.sitePrimaryColor=null}},ratioValue(e){if(!e||typeof e!="string")return null;const[t,i]=e.split("/").map(Number);return!t||!i?null:`${t} / ${i}`},colorValue(e){return e?typeof e==="string"?e:e.value||e.hex||e.color||null:null}}};var L=function(){var t=this,i=t._self._c;return i("k-block-figure",{staticClass:"k-block-type-pure-image",attrs:{"is-empty":!t.image.url,"empty-icon":"image","empty-text":"Select an image …"},on:{open:t.open,update:t.update}},[i("figure",{staticClass:"pure-image-preview"},[t.lightbox?i("div",{staticClass:"pure-media-preview__indicators"},[i("span",{staticClass:"pure-preview__indicator"},[t._v(" Lightbox ")])]):t._e(),i("div",{staticClass:"pure-image-preview__frame",class:t.frameClasses,style:t.frameStyle},[t.image.url?i("img",{class:t.imageClasses,style:t.imageStyle,attrs:{src:t.previewUrl||t.image.url,alt:t.content.alt||""}}):t._e()]),t.content.caption?i("figcaption",{staticClass:"pure-image-preview__caption",style:t.captionStyle,domProps:{innerHTML:t._s(t.content.caption)}}):t._e()])])},N=[],R=o(A,L,N);const E=R.exports,O={computed:{video(){var e;return((e=this.content.video)==null?void 0:e[0])||{}},poster(){var e;return((e=this.content.poster)==null?void 0:e[0])||{}},isExternal(){return this.content.location==="web"&&!!this.content.url},isEmpty(){return!this.video.url&&!this.isExternal},autoplay(){return this.toBool(this.content.autoplay,!0)},muted(){return this.toBool(this.content.muted,!0)||this.autoplay},loop(){return this.toBool(this.content.loop,!0)},controls(){return this.toBool(this.content.controls,!1)},activateLightbox(){return this.toBool(this.content.activatelightbox,!1)},indicators(){const e=[];return this.poster.url&&!this.isExternal&&e.push("Poster"),this.autoplay&&!this.isExternal&&e.push("Autoplay"),this.loop&&!this.isExternal&&e.push("Loop"),this.controls&&!this.isExternal&&e.push("Controls"),this.activateLightbox&&!this.isExternal&&e.push("Lightbox"),e},frameClasses(){return{"is-rounded":this.content.corners==="rounded","is-circle":this.content.corners==="circle"}},frameStyle(){const e=Number(this.content.width),t=Number(this.content.height);return e>0&&t>0?{aspectRatio:`${e} / ${t}`}:{}} ,mediaStyle(){const e=["contain","cover","fill","none","scale-down"],t=["left top","center top","right top","left center","center center","right center","left bottom","center bottom","right bottom"],i=e.includes(this.content.objectfit)?this.content.objectfit:"contain",s=t.includes(this.content.objectposition)?this.content.objectposition:"center center";return{objectFit:i,objectPosition:s}},captionStyle(){const e=this.content.captiontextalign||"left";return{textAlign:["left","center","right"].includes(e)?e:"left"}}},methods:{toBool(e,t=!1){return e==null||e===""?t:e===!0||e===1||e==="1"||e==="true"}}};var T=function(){var t=this,i=t._self._c;return i("k-block-figure",{staticClass:"k-block-type-pure-video",attrs:{"is-empty":t.isEmpty,"empty-icon":"video","empty-text":"Select a video …"},on:{open:t.open,update:t.update}},[i("figure",{staticClass:"pure-video-preview"},[t.indicators.length?i("div",{staticClass:"pure-media-preview__indicators"},t._l(t.indicators,function(s){return i("span",{key:s,staticClass:"pure-preview__indicator"},[t._v(" "+t._s(s)+" ")])}),0):t._e(),i("div",{staticClass:"pure-video-preview__frame",class:t.frameClasses,style:t.frameStyle},[t.video.url?i("video",{staticClass:"pure-video-preview__media",style:t.mediaStyle,attrs:{src:t.video.url,poster:t.poster.url||null,autoplay:t.autoplay,loop:t.loop,controls:t.controls,muted:"",playsinline:"",preload:"auto"},domProps:{muted:!0}}):t.poster.url?i("img",{staticClass:"pure-video-preview__poster",style:t.mediaStyle,attrs:{src:t.poster.url,alt:""}}):t.isExternal?i("div",{staticClass:"pure-video-preview__external"},[i("k-icon",{attrs:{type:"video"}}),i("span",[t._v("External video")])],1):t._e()]),t.content.caption?i("figcaption",{staticClass:"pure-video-preview__caption",style:t.captionStyle,domProps:{innerHTML:t._s(t.content.caption)}}):t._e()])])},V=[],H=o(O,T,V);const j=H.exports,W={data(){return{activeIndex:0}},computed:{slides(){return Array.isArray(this.content.images)?this.content.images.filter(e=>e==null?void 0:e.url):[]},currentSlide(){return this.slides[this.activeIndex]||this.slides[0]||null},loop(){return this.toBool(this.content.loop,!1)},rewind(){return this.toBool(this.content.rewind,!1)},autoplay(){return this.toBool(this.content.autoplay,!1)},pagination(){return this.toBool(this.content.pagination,!1)},navigation(){return this.toBool(this.content.navigation,!1)},scrollbar(){return this.toBool(this.content.scrollbar,!1)},mousewheel(){return this.toBool(this.content.enablemousewheel,!1)},draggable(){return this.toBool(this.content.draggable,!1)},captionStyle(){const e=this.content.captiontextalign||"left";return{textAlign:["left","center","right"].includes(e)?e:"left"}},indicators(){const e=[];this.content.effect&&this.content.effect!=="slide"&&e.push(this.content.effect),this.loop&&e.push("Loop"),this.rewind&&e.push("Rewind"),this.autoplay&&e.push("Autoplay"),this.pagination&&e.push("Pagination"),this.navigation&&e.push("Navigation"),this.scrollbar&&e.push("Scrollbar"),this.mousewheel&&e.push("Mousewheel"),this.draggable&&e.push("Draggable");const t=Number(this.content.slidesperview||1);return t>1&&e.push(`${t} slides`),e},stageStyle(){const e=this.content.aspectratio||"auto";return e==="auto"?{}:{aspectRatio:String(e).replace(":"," / ")}}},watch:{slides(){this.activeIndex>=this.slides.length&&(this.activeIndex=0)}},methods:{toBool(e,t=!1){return e==null||e===""?t:e===!0||e===1||e==="1"||e==="true"},isVideo(e){const t=(e==null?void 0:e.mime)||"",i=(e==null?void 0:e.type)||"",s=(e==null?void 0:e.extension)||"";return i==="video"||t.startsWith("video/")||["mp4","webm","ogg","mov","m4v"].includes(s.toLowerCase())},previous(){this.slides.length&&(this.activeIndex=(this.activeIndex-1+this.slides.length)%this.slides.length)},next(){this.slides.length&&(this.activeIndex=(this.activeIndex+1)%this.slides.length)}}};var D=function(){var t=this,i=t._self._c;return i("k-block-figure",{staticClass:"k-block-type-pure-swiper",attrs:{"is-empty":!t.slides.length,"empty-icon":"images","empty-text":"Select images or videos …"},on:{open:t.open,update:t.update}},[i("figure",{staticClass:"pure-swiper-preview"},[t.indicators.length?i("div",{staticClass:"pure-media-preview__indicators"},t._l(t.indicators,function(s){return i("span",{key:s,staticClass:"pure-preview__indicator"},[t._v(" "+t._s(s)+" ")])}),0):t._e(),t.slides.length?i("div",{staticClass:"pure-swiper-preview__stage",style:t.stageStyle},[t.currentSlide?[t.isVideo(t.currentSlide)?i("video",{staticClass:"pure-swiper-preview__media",attrs:{src:t.currentSlide.url,muted:"",playsinline:"",preload:"metadata"},domProps:{muted:!0}}):i("img",{staticClass:"pure-swiper-preview__media",attrs:{src:t.currentSlide.url,alt:""}})]:t._e(),t.slides.length>1?i("button",{staticClass:"pure-swiper-preview__button pure-swiper-preview__button--prev",attrs:{type:"button","aria-label":"Previous slide"},on:{click:function(s){return s.stopPropagation(),t.previous.apply(null,arguments)}}},[t._v(" ‹ ")]):t._e(),t.slides.length>1?i("button",{staticClass:"pure-swiper-preview__button pure-swiper-preview__button--next",attrs:{type:"button","aria-label":"Next slide"},on:{click:function(s){return s.stopPropagation(),t.next.apply(null,arguments)}}},[t._v(" › ")]):t._e(),t.slides.length>1?i("div",{staticClass:"pure-swiper-preview__pagination"},t._l(t.slides,function(s,r){return i("span",{key:r,staticClass:"pure-swiper-preview__dot",class:{"is-active":r===t.activeIndex}})}),0):t._e()],2):t._e(),t.content.caption?i("figcaption",{staticClass:"pure-swiper-preview__caption",style:t.captionStyle,domProps:{innerHTML:t._s(t.content.caption)}}):t._e()])])},K=[],q=o(W,D,K);const G=q.exports,U={data(){return{items:[],previewWidth:0,loading:!0,error:null,resizeObserver:null,layoutFrame:null}},computed:{sourcePage(){return(Array.isArray(this.content.sourcepage)?this.content.sourcepage:[])[0]||null},sourceKey(){var e;return((e=this.sourcePage)==null?void 0:e.id)||"site"},displayMode(){const e=this.content.displaymode??this.content.displayMode??"grid",t=typeof e=="object"?e.value??e.id??e.text??"grid":e;return String(t).toLowerCase().includes("masonry")?"masonry":"grid"},widthMode(){const e=this.content.widthmode??this.content.widthMode??"span",t=typeof e=="object"?e.value??e.id??e.text??"span":e;const i=String(t).toLowerCase();return i.includes("min")?"minmax":"span"},tagFilter(){return this.toBool(this.content.tagfilter,!1)},dateFilter(){return this.toBool(this.content.datefilter,!1)},filterMulti(){return this.toBool(this.content.filtermulti,!1)},filterSticky(){return this.toBool(this.content.filtersticky,!1)},itemScaler(){return this.toBool(this.content.itemscaler,!1)},showTags(){return this.toBool(this.content.showtags,!1)},showGroups(){return this.toBool(this.content.showgroups,!1)},showPageTitle(){return this.toBool(this.content.showpagetitle,!1)},rowGap(){const e=this.content.rowgap??this.content.rowGap??this.content.gap??"s";return typeof e=="object"?e.value??e.id??e.text??"s":String(e)},columnGap(){const e=this.content.columngap??this.content.columnGap??this.content.gap??"s";return typeof e=="object"?e.value??e.id??e.text??"s":String(e)},previewRowGap(){return{none:"0px",xxs:"0.375rem",xs:"0.75rem",s:"1rem",m:"1.625rem",l:"2.625rem",xl:"4.236rem",xxl:"6.854rem","3xl":"11.09rem","4xl":"17.944rem"}[this.rowGap]||"1rem"},previewColumnGap(){return{none:"0px",xxs:"0.375rem",xs:"0.75rem",s:"1rem",m:"1.625rem",l:"2.625rem",xl:"4.236rem",xxl:"6.854rem","3xl":"11.09rem","4xl":"17.944rem"}[this.columnGap]||"1rem"},gridStyle(){return this.displayMode==="masonry"?{display:"block",position:"relative",rowGap:this.previewRowGap,columnGap:this.previewColumnGap}:{display:"grid",gridTemplateColumns:"repeat(4, minmax(0, 1fr))",alignItems:"start",rowGap:this.previewRowGap,columnGap:this.previewColumnGap}},indicators(){const e=[this.displayMode==="masonry"?"Masonry":"Grid",this.widthMode==="minmax"?"Min–Max Width":"Span Width"];return this.tagFilter&&e.push("Tag Filter"),this.dateFilter&&e.push("Date Filter"),this.filterMulti&&e.push("Multi-select"),this.filterSticky&&e.push("Sticky Filter"),this.itemScaler&&e.push("Scaler"),this.showTags&&e.push("Tags"),this.showGroups&&e.push("Groups"),this.showPageTitle&&e.push("Page Title"),e},prefilteredItems(){let e=[...this.items];const t=this.normalizeTags(this.content.filterbytags),i=this.normalizeTags(this.content.filterbygroups);t.length&&(e=e.filter(s=>(s.tags||[]).some(r=>t.includes(r)))),i.length&&(e=e.filter(s=>(s.groups||[]).some(r=>i.includes(r)))),this.toBool(this.content.ordertoggle,!1)&&e.reverse();const n=Number(this.content.limititems||0);return n>0&&(e=e.slice(0,n)),e},visibleItems(){return this.prefilteredItems}},watch:{sourceKey(){this.loadItems()},displayMode(){this.scheduleLayout()},rowGap(){this.scheduleLayout()},columnGap(){this.scheduleLayout()},visibleItems(){this.scheduleLayout()}},mounted(){this.resizeObserver=new ResizeObserver(([e])=>{this.previewWidth=(e==null?void 0:e.contentRect.width)||this.$refs.preview?.clientWidth||0,this.scheduleLayout()}),this.$refs.preview&&this.resizeObserver.observe(this.$refs.preview),this.$panel?.events?.on?.("page.sort",this.onPageChange),this.$panel?.events?.on?.("page.changeStatus",this.onPageChange),this.loadItems()},beforeDestroy(){this.$panel?.events?.off?.("page.sort",this.onPageChange),this.$panel?.events?.off?.("page.changeStatus",this.onPageChange),this.resizeObserver&&this.resizeObserver.disconnect(),this.layoutFrame&&cancelAnimationFrame(this.layoutFrame)},methods:{onPageChange(){this.loadItems()},toBool(e,t=!1){return e==null||e===""?t:e===!0||e===1||e==="1"||e==="true"},normalizeTags(e){return Array.isArray(e)?e.map(t=>typeof t=="string"?t:(t==null?void 0:t.value)||(t==null?void 0:t.text)||"").map(t=>t.trim()).filter(Boolean):typeof e=="string"?e.split(",").map(t=>t.trim()).filter(Boolean):[]},async loadItems(){this.loading=!0,this.error=null;try{const e=encodeURIComponent(this.sourceKey.replaceAll("/","+")),t=await this.$api.get(`organizer-preview/${e}`);this.items=Array.isArray(t==null?void 0:t.items)?t.items:[]}catch(e){console.error("Could not load organizer preview:",e),this.items=[],this.error="Could not load organizer entries."}finally{this.loading=!1,this.scheduleLayout()}},itemSpan(e){return Math.max(1,Math.min(12,Number(e.thumbnailWidth||6)))},itemStyle(){return{}},organizerElements(){const e=this.$refs.grid;return e?Array.from(e.querySelectorAll(":scope > .pure-organizer-preview__item")):[]},resetMasonry(){const e=this.$refs.grid,t=this.organizerElements();e&&(e.style.height=""),t.forEach(i=>{i.style.position="",i.style.left="",i.style.top="",i.style.width=""})},layoutMasonry(){const e=this.$refs.grid,t=this.organizerElements();if(!e||!t.length)return;if(this.displayMode!=="masonry"){this.resetMasonry();return}const i=e.clientWidth;if(!i)return;const a=e=>{const t=String(e||"0").trim(),i=Number.parseFloat(t);if(!Number.isFinite(i))return 0;if(t.endsWith("rem")){const e=Number.parseFloat(getComputedStyle(document.documentElement).fontSize)||16;return i*e}return i},s=a(this.previewColumnGap),n=a(this.previewRowGap),r=4,d=Math.max(0,(i-s*(r-1))/r),m=d+s,u=new Array(r).fill(0);t.forEach(e=>{let t=0;for(let i=1;i<r;i++)u[i]<u[t]&&(t=i);e.style.position="absolute",e.style.width=`${d}px`,e.style.left=`${t*m}px`,e.style.top=`${u[t]}px`;const i=e.getBoundingClientRect().height;u[t]+=i+n});const l=Math.max(0,...u)-n;e.style.height=`${Math.max(0,l)}px`},scheduleLayout(){this.$nextTick(()=>{this.layoutFrame&&cancelAnimationFrame(this.layoutFrame),this.layoutFrame=requestAnimationFrame(()=>this.layoutMasonry())})},thumbnailType(e){return e?e.type||(e.url?"image":null):null},firstSwiperSlide(e){return Array.isArray(e==null?void 0:e.slides)?e.slides.find(t=>t==null?void 0:t.url)||null:null},hasThumbnail(e){const t=this.thumbnailType(e);return t==="image"?!!(e!=null&&e.url):t==="video"?!!(e!=null&&(e.url||e.poster||e.external)):t==="swiper"?!!this.firstSwiperSlide(e):!1},thumbnailClasses(e){return{"is-rounded":e.corners==="rounded","is-circle":e.corners==="circle","has-border":e.border===!0}},thumbnailFrameStyle(e){const t={},i=this.thumbnailType(e);return i==="image"&&e.crop&&e.ratio&&(t.aspectRatio=String(e.ratio).replace("/"," / ")),i==="video"&&Number(e.width)>0&&Number(e.height)>0&&(t.aspectRatio=`${e.width} / ${e.height}`),i==="swiper"&&e.aspectRatio&&e.aspectRatio!=="auto"&&(t.aspectRatio=String(e.aspectRatio).replace(":"," / ")),e.border&&e.borderColor&&(t.borderColor=e.borderColor),t},thumbnailImageStyle(e){const t=this.thumbnailType(e);return t==="video"?{objectFit:e.objectFit||"contain",objectPosition:e.objectPosition||"center center"}:{objectFit:t==="swiper"?e.imageFit||"cover":e.crop?"cover":"contain"}}}};var Y=function(){var t=this,i=t._self._c;return i("k-block-figure",{staticClass:"k-block-type-pure-organizer",attrs:{"is-empty":!t.loading&&!t.visibleItems.length,"empty-icon":"grid","empty-text":"No entries found."},on:{open:t.open,update:t.update}},[i("div",{ref:"preview",staticClass:"pure-organizer-preview"},[t.indicators.length?i("div",{staticClass:"pure-media-preview__indicators pure-organizer-preview__indicators"},t._l(t.indicators,function(s){return i("span",{key:s,staticClass:"pure-preview__indicator"},[t._v(" "+t._s(s)+" ")])}),0):t._e(),t.loading?i("div",{staticClass:"pure-organizer-preview__status"},[t._v(" Loading entries … ")]):t.error?i("div",{staticClass:"pure-organizer-preview__status"},[t._v(" "+t._s(t.error)+" ")]):t.visibleItems.length?i("div",{ref:"grid",staticClass:"pure-organizer-preview__grid",class:[`is-${t.displayMode}`,`is-width-${t.widthMode}`],style:t.gridStyle},t._l(t.visibleItems,function(s){return i("article",{key:s.id,ref:"item",refInFor:!0,staticClass:"pure-organizer-preview__item",style:t.itemStyle(s)},[t.hasThumbnail(s.thumbnail)?i("div",{staticClass:"pure-organizer-preview__media",class:t.thumbnailClasses(s.thumbnail),style:t.thumbnailFrameStyle(s.thumbnail)},[t.thumbnailType(s.thumbnail)==="image"?i("img",{style:t.thumbnailImageStyle(s.thumbnail),attrs:{src:s.thumbnail.url,alt:s.thumbnail.alt||""},on:{load:t.scheduleLayout}}):t.thumbnailType(s.thumbnail)==="video"&&s.thumbnail.url?i("video",{style:t.thumbnailImageStyle(s.thumbnail),attrs:{src:s.thumbnail.url,poster:s.thumbnail.poster||null,muted:"",autoplay:"",loop:"",playsinline:"",preload:"metadata"},on:{loadedmetadata:t.scheduleLayout}}):t.thumbnailType(s.thumbnail)==="video"&&s.thumbnail.poster?i("img",{style:t.thumbnailImageStyle(s.thumbnail),attrs:{src:s.thumbnail.poster,alt:""},on:{load:t.scheduleLayout}}):t.thumbnailType(s.thumbnail)==="swiper"&&t.firstSwiperSlide(s.thumbnail)?t.firstSwiperSlide(s.thumbnail).type==="video"?i("video",{style:t.thumbnailImageStyle(s.thumbnail),attrs:{src:t.firstSwiperSlide(s.thumbnail).url,muted:"",autoplay:"",loop:"",playsinline:"",preload:"metadata"},on:{loadedmetadata:t.scheduleLayout}}):i("img",{style:t.thumbnailImageStyle(s.thumbnail),attrs:{src:t.firstSwiperSlide(s.thumbnail).url,alt:t.firstSwiperSlide(s.thumbnail).alt||""},on:{load:t.scheduleLayout}}):t.thumbnailType(s.thumbnail)==="video"&&s.thumbnail.external?i("div",{staticClass:"pure-organizer-preview__external-video"},[t._v(" External video ")]):t._e()]):i("div",{staticClass:"pure-organizer-preview__no-thumbnail"},[t._v(" No thumbnail found ")])])}),0):t._e()])])},J=[],Q=o(U,Y,J);const X=Q.exports;panel.plugin("kirbypure/blocks",{blocks:{"pure-site-header-navigation":C,"pure-site-footer-navigation":P,"pure-spacer":F,"pure-image":E,"pure-video":j,"pure-swiper":G,"pure-organizer":X}})})();

;(() => {
  const PureFlexibleTextPreview = {
    props: {
      content: Object
    },

    computed: {
      textStyle() {
        const style = {
          fontSize: this.previewFontSize
        };

        const fontFamily = this.cleanFontFamily(this.content && this.content.fontfamily);
        const lineHeight = this.cleanDimension(this.content && this.content.lineheight, "line-height");
        const letterSpacing = this.cleanDimension(this.content && this.content.letterspacing, "letter-spacing");
        const textAlignments = ["left", "center", "right", "justify"];
        const textAlign = (this.content && this.content.textalign) || "left";

        style.fontFamily = fontFamily || '"TeX Gyre Heros", helvetica, arial, sans-serif';
        style.textAlign = textAlignments.includes(textAlign) ? textAlign : "left";
        if (lineHeight) style.lineHeight = lineHeight;
        if (letterSpacing) style.letterSpacing = letterSpacing;

        return style;
      },

      previewFontSize() {
        const sizes = {
          xxs: 0.472,
          xs: 0.764,
          s: 1,
          sm: 1.309,
          m: 1.618,
          ml: 2.118,
          l: 2.618,
          xl: 4.236,
          xxl: 6.854,
          "3xl": 11.09,
          "4xl": 17.944
        };
        const size = (this.content && this.content.fontsize) || "s";
        return `${sizes[size] || sizes.s}rem`;
      }
    },

    methods: {
      cleanFontFamily(value) {
        return String(value || "").trim().replace(/[;{}<>]/g, "");
      },

      cleanDimension(value, type) {
        const normalized = String(value || "")
          .trim()
          .replace(/(\d)\s+(?=[a-z%])/gi, "$1");

        if (!normalized) return "";

        const pattern = type === "line-height"
          ? /^(?:normal|(?:\d+(?:\.\d*)?|\.\d+)(?:px|em|rem|vw|vh|%)?)$/i
          : /^(?:normal|[-+]?(?:\d+(?:\.\d*)?|\.\d+)(?:px|em|rem|vw|vh|%)?)$/i;

        return pattern.test(normalized) ? normalized : "";
      }
    },

    render(h) {
      return h("k-block-figure", {
        class: "k-block-type-pure-flexible-text",
        props: {
          isEmpty: !(this.content && this.content.text),
          emptyIcon: "text",
          emptyText: "Enter text …"
        },
        on: {
          open: this.open,
          update: this.update
        }
      }, [
        h("div", {
          class: "pure-flexible-text-preview",
          style: this.textStyle,
          domProps: {
            innerHTML: (this.content && this.content.text) || ""
          }
        })
      ]);
    }
  };

  panel.plugin("kirbypure/flexible-text", {
    blocks: {
      "pure-flexible-text": PureFlexibleTextPreview
    }
  });
})();

/* Kirby Pure Blocks: layout settings preview */
(() => {
  const spaces = {
    none: "0px", xxs: "0.375rem", xs: "0.75rem", s: "1rem", m: "1.625rem",
    l: "2.625rem", xl: "4.236rem", xxl: "6.854rem", "3xl": "11.09rem", "4xl": "17.944rem"
  };
  const space = (value, fallback = "none") => spaces[String(value ?? fallback).toLowerCase()] ?? spaces[fallback] ?? "0px";
  const attr = (attrs, name, fallback = null) => {
    if (!attrs || typeof attrs !== "object") return fallback;
    if (attrs[name] !== undefined) return attrs[name];
    const wanted = name.toLowerCase();
    const key = Object.keys(attrs).find((key) => key.toLowerCase() === wanted);
    return key ? attrs[key] : fallback;
  };
  const bool = (value) => value === true || value === 1 || value === "1" || value === "true";
  const color = (value) => {
    if (Array.isArray(value)) value = value[0];
    if (value && typeof value === "object") value = value.value ?? value.hex ?? null;
    return typeof value === "string" && value.trim() ? value.trim() : null;
  };
  const clear = (layout) => {
    layout.classList.remove("pure-layout-preview", "pure-layout-preview--full-bleed");
    ["margin-top","margin-bottom","margin-sides","padding-top","padding-bottom","padding-sides","column-gap","row-gap","background","max-width","max-width-margin-left","max-width-margin-right","align-items"]
      .forEach((name) => layout.style.removeProperty(`--pure-layout-preview-${name}`));
    delete layout.dataset.pureBackgroundFullBleed;
  };
  const apply = (layout, attrs = {}) => {
    clear(layout);
    layout.classList.add("pure-layout-preview");
    const fullBleed = bool(attr(attrs, "layoutSectionFullBleed", false));
    const hasMaxWidth = bool(attr(attrs, "maxLayoutSectionWidthToggle", false));
    const maxWidth = Number(attr(attrs, "maxLayoutSectionWidthValue", 0));
    const alignment = String(attr(attrs, "maxLayoutSectionWidthAlignment", "center"));
    const background = color(attr(attrs, "backgroundColor"));
    const backgroundFullBleed = bool(attr(attrs, "backgroundColorFullBleed", false));
    layout.classList.toggle("pure-layout-preview--full-bleed", fullBleed);
    layout.style.setProperty("--pure-layout-preview-margin-top", space(attr(attrs, "marginTop", "none")));
    layout.style.setProperty("--pure-layout-preview-margin-bottom", space(attr(attrs, "marginBottom", "none")));
    layout.style.setProperty("--pure-layout-preview-margin-sides", space(attr(attrs, "marginLeftRight", "none")));
    layout.style.setProperty("--pure-layout-preview-padding-top", space(attr(attrs, "paddingTop", "none")));
    layout.style.setProperty("--pure-layout-preview-padding-bottom", space(attr(attrs, "paddingBottom", "none")));
    layout.style.setProperty("--pure-layout-preview-padding-sides", space(attr(attrs, "paddingLeftRight", "none")));
    layout.style.setProperty("--pure-layout-preview-column-gap", space(attr(attrs, "columnGap", "none")));
    layout.style.setProperty("--pure-layout-preview-row-gap", space(attr(attrs, "rowGap", "s")));
    layout.style.setProperty("--pure-layout-preview-align-items", String(attr(attrs, "alignItems", "start")));
    if (background) {
      layout.style.setProperty("--pure-layout-preview-background", background);
      layout.dataset.pureBackgroundFullBleed = backgroundFullBleed ? "true" : "false";
    }
    if (!fullBleed && hasMaxWidth && maxWidth > 0) {
      layout.style.setProperty("--pure-layout-preview-max-width", `${maxWidth}px`);
      const margins = { left: ["0", "auto"], center: ["auto", "auto"], right: ["auto", "0"] }[alignment] || ["auto", "auto"];
      layout.style.setProperty("--pure-layout-preview-max-width-margin-left", margins[0]);
      layout.style.setProperty("--pure-layout-preview-max-width-margin-right", margins[1]);
    }
  };

  panel.plugin("kirbypure/layout-preview", {
    components: {
      "k-layout-field": {
        extends: "k-layout-field",
        watch: {
          value: {
            deep: true,
            handler() { this.$nextTick(this.applyPureLayoutPreview); }
          }
        },
        mounted() { this.$nextTick(this.applyPureLayoutPreview); },
        updated() { this.$nextTick(this.applyPureLayoutPreview); },
        methods: {
          applyPureLayoutPreview() {
            const layouts = Array.from(this.$el?.querySelectorAll?.(".k-layout") || []);
            const values = Array.isArray(this.value) ? this.value : [];
            layouts.forEach((layout, index) => apply(layout, values[index]?.attrs || {}));
          }
        }
      }
    }
  });
})();

/* Kirby Pure Blocks: Pure Button preview */
(() => {
  const colorValue = (value) => {
    if (Array.isArray(value)) value = value[0];
    if (value && typeof value === "object") value = value.value ?? value.hex ?? value.color ?? null;
    return typeof value === "string" && value.trim() ? value.trim() : null;
  };

  const PureButtonPreview = {
    props: { content: Object },
    data() { return { siteSettings: {} }; },
    computed: {
      buttonText() { return String((this.content && this.content.text) || "").trim(); },
      previewFontSize() {
        const sizes = { xxs:.472, xs:.764, s:1, sm:1.309, m:1.618, ml:2.118, l:2.618, xl:4.236, xxl:6.854, "3xl":11.09, "4xl":17.944 };
        const size = (this.content && this.content.fontsize) || "s";
        return `${sizes[size] || sizes.s}rem`;
      },
      buttonRadius() {
        const shape = this.setting("buttonshape", "buttonShape") || "pillshape";
        if (shape === "squared") return "0";
        if (shape === "rounded") return ".472rem";
        return "9999px";
      },
      buttonPadding() {
        const value = this.setting("buttonpadding", "buttonPadding") || "var(--size-xs) var(--size-s) calc(var(--size-xs) + 2px) var(--size-s)";
        return String(value)
          .replaceAll("var(--size-xxs)", ".472rem")
          .replaceAll("var(--size-xs)", ".764rem")
          .replaceAll("var(--size-s)", "1rem")
          .replaceAll("var(--size-sm)", "1.309rem")
          .replaceAll("var(--size-m)", "1.618rem")
          .replaceAll("var(--size-ml)", "2.118rem")
          .replaceAll("var(--size-l)", "2.618rem");
      },
      previewVariables() {
        const primary = colorValue(this.setting("colorprimary", "colorPrimary")) || "#000000";
        const secondary = colorValue(this.setting("colorsecondary", "colorSecondary")) || "#ffffff";
        const resolve = (value, fallback) => {
          const v = colorValue(value) || fallback;
          if (v === "var(--color-primary)") return primary;
          if (v === "var(--color-secondary)") return secondary;
          return v;
        };
        return {
          "--pure-preview-btn-color": resolve(this.setting("buttontextcolor", "buttonTextColor"), "var(--color-primary)"),
          "--pure-preview-btn-background": resolve(this.setting("buttonbackgroundcolor", "buttonBackgroundColor"), "var(--color-secondary)"),
          "--pure-preview-btn-border": resolve(this.setting("buttonbordercolor", "buttonBorderColor"), "var(--color-primary)"),
          "--pure-preview-btn-hover-color": resolve(this.setting("buttonhovertextcolor", "buttonHoverTextColor"), "var(--color-secondary)"),
          "--pure-preview-btn-hover-background": resolve(this.setting("buttonhoverbackgroundcolor", "buttonHoverBackgroundColor"), "var(--color-primary)"),
          "--pure-preview-btn-hover-border": resolve(this.setting("buttonhoverbordercolor", "buttonHoverBorderColor"), "var(--color-primary)"),
          "--pure-preview-btn-radius": this.buttonRadius,
          "--pure-preview-btn-padding": this.buttonPadding
        };
      }
    },
    mounted() { this.loadSiteSettings(); },
    methods: {
      async loadSiteSettings() {
        try {
          const response = await this.$api.get("site");
          const site = (response && response.data) || response || {};
          this.siteSettings = site.content || {};
        } catch (error) {
          console.warn("Pure Button: Could not load site button settings.", error);
          this.siteSettings = {};
        }
      },
      setting(lowercase, camelcase) {
        return this.siteSettings[lowercase] ?? this.siteSettings[camelcase] ?? null;
      }
    },
    render(h) {
      return h("k-block-figure", {
        class: "k-block-type-pure-button",
        props: { isEmpty: !this.buttonText, emptyIcon: "url", emptyText: "Enter button text …" },
        on: { open: this.open, update: this.update }
      }, [
        h("div", { class: "pure-button-preview", style: this.previewVariables }, [
          h("span", { class: "pure-button-preview__button", style: { fontSize: this.previewFontSize } }, this.buttonText || "Button")
        ])
      ]);
    }
  };

  panel.plugin("kirbypure/button-preview", { blocks: { "pure-button": PureButtonPreview } });
})();
