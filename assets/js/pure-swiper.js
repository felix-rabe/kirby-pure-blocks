document.addEventListener('DOMContentLoaded', () => {
    const swiperContainers = document.querySelectorAll('.pure-swiper .swiper');

    swiperContainers.forEach(container => {
        // Retrieve settings from data attributes
        const effect = container.dataset.effect || 'slide';
        // ---- NEW: hide main overflow-x if effect is "cards" ----
        if (effect === 'cards') {
            const mainEl = document.querySelector('main');
            if (mainEl) mainEl.style.overflowX = 'hidden';
        }
        const loop = effect !== 'cards' && container.dataset.loop === 'true';
        const rewind = container.dataset.rewind === 'true';
        const draggable = container.dataset.draggable === 'true';
        const slidesPerView = parseFloat(container.dataset.slidesPerView) || 1;
        const enablePagination = container.dataset.pagination === 'true';
        const paginationPlacement = container.dataset.paginationPlacement || 'inside';
        const enableNavigation = container.dataset.navigation === 'true';
        const enableScrollbar = container.dataset.scrollbar === 'true';
        const enableAutoplay = container.dataset.autoplay === 'true';
        const autoplayDelay = parseInt(container.dataset.autoplayDelay, 10);
        const finalAutoplayDelay = isNaN(autoplayDelay) ? 3000 : autoplayDelay;
        const autoplayDisableOnInteraction = container.dataset.autoplayDisableOnInteraction === 'true';
        const autoplayPauseOnMouseEnter = container.dataset.autoplayPauseOnMouseEnter === 'true';
        const parsedSpeed = parseInt(container.dataset.speed, 10);
        const speed = Number.isFinite(parsedSpeed) && parsedSpeed >= 0
            ? Math.max(1, parsedSpeed)
            : 1000;
        const selectedAspectRatio = container.dataset.aspectRatio || 'auto';
        const imageObjectFit = container.dataset.objectFit || 'contain';
        const enableAutoHeight = container.dataset.autoHeight === 'true';
        const swiperId = `#${container.id}`;
        const enableMousewheel = container.dataset.enableMousewheel === 'true';

        // Apply aspect-ratio and object-fit styles
        if (selectedAspectRatio !== 'auto' && selectedAspectRatio !== 'fullscreen') {
            container.style.aspectRatio = selectedAspectRatio;
            container.style.height = 'auto';
            container.style.minHeight = '0';
        } else if (selectedAspectRatio === 'fullscreen') {
            container.classList.add('fill-window');
        } else {
            container.style.aspectRatio = '';
            container.style.height = 'auto';
            container.style.minHeight = '0';
        }

        const mediaInSwiper = container.querySelectorAll('.swiper-slide img, .swiper-slide video');
        mediaInSwiper.forEach(el => {
            el.style.objectFit = imageObjectFit;
            el.style.width = '100%';
            el.style.height = '100%';
            el.style.display = 'block';
        });

        const figuresInSwiper = container.querySelectorAll('.swiper-slide figure');
        figuresInSwiper.forEach(figure => {
            figure.style.width = '100%';
            figure.style.height = '100%';
            figure.style.display = 'block';
        });

        // Configure Swiper options
        const swiperOptions = {
            lazy: true,
            effect: effect,
            fadeEffect: { crossFade: true },
            coverflowEffect: { slideShadows: false },
            cubeEffect: { shadow: false, slideShadows: false },
            flipEffect: { slideShadows: false },
            cardsEffect: { slideShadows: false },
            creativeEffect: {
                prev: { shadow: false, translate: [0, 0, -400] },
                next: { translate: ["100%", 0, 0] },
            },
            direction: 'horizontal',
            speed: speed,
            spaceBetween: (effect === 'slide' || effect === 'coverflow') ? (container.dataset.spaceBetween || 0) : 0,
            loop: loop,
            grabCursor: draggable,
            allowTouchMove: draggable,
            slidesPerView: (effect === 'slide' || effect === 'coverflow') ? slidesPerView : 1,
            autoHeight: enableAutoHeight,
            rewind: loop ? false : rewind,
            navigation: enableNavigation ? {
                nextEl: `${swiperId} .swiper-button-next`,
                prevEl: `${swiperId} .swiper-button-prev`,
            } : false,
            pagination: enablePagination ? {
                el: paginationPlacement === 'outside' ? `.swiper-pagination-${container.id}-outside` : `${swiperId} .swiper-pagination`,
                clickable: true,
            } : false,
            scrollbar: enableScrollbar ? {
                el: `${swiperId} .swiper-scrollbar`,
                draggable: true,
            } : false,
        };

        // Add autoplay if enabled
        if (enableAutoplay) {
            swiperOptions.autoplay = {
                delay: finalAutoplayDelay,
                disableOnInteraction: autoplayDisableOnInteraction,
                pauseOnMouseEnter: autoplayPauseOnMouseEnter,
            };
        }

        // Conditional mousewheel
        if (enableMousewheel) {
            swiperOptions.mousewheel = {
                forceToAxis: true,
                releaseOnEdges: true
                // optional: sensitivity, invert
            };
        }

        // Initialize Swiper
        new Swiper(swiperId, swiperOptions);
    });
});
