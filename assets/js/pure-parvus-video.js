/**
 * Parvus integration for Pure image + Kirby-hosted video lightboxes.
 *
 * Parvus remains responsible for the gallery, overlay and transitions.
 * For a Pure Video trigger, its helper image is retained for Parvus' own
 * bookkeeping while a real <video> is mounted into the active slide.
 */
document.addEventListener("DOMContentLoaded", () => {
    let prvs = null;
    let observer = null;
    let activeTrigger = null;

    const selector = '.pure-image-desktop a.lightbox, .pure-video-desktop a.lightbox';

    // Capture the actual video trigger before Parvus handles the click.
    // This is intentionally independent of Parvus' custom-event payload.
    document.addEventListener('click', event => {
        const trigger = event.target.closest(selector);
        if (!trigger) return;
        // Always update the pending source. Clicking an image after a video must
        // explicitly clear the previous video trigger instead of reusing it.
        activeTrigger = trigger.dataset?.parvusVideo ? trigger : null;
    }, true);

    const boolData = (el, name) => el?.dataset?.[name] === '1';

    const activeSlide = () =>
        document.querySelector('.parvus__slide[aria-hidden="false"]');

    function eventSource(event) {
        // Parvus 2.x exposes open as detail.source, while select in the
        // bundled build is nested once more as detail.detail.source.
        return event?.detail?.source || event?.detail?.detail?.source || null;
    }

    function removeMountedVideos(exceptSlide = null) {
        document.querySelectorAll('.parvus__slide .pure-parvus-video').forEach(video => {
            if (exceptSlide && exceptSlide.contains(video)) return;
            video.pause();
            video.remove();
        });
        document.querySelectorAll('.parvus__slide img.is-video-mounted').forEach(img => {
            if (exceptSlide && exceptSlide.contains(img)) return;
            img.classList.remove('is-video-mounted', 'pure-parvus-video-placeholder');
        });
    }

    function finishVideoClose() {
        const videos = Array.from(document.querySelectorAll('.parvus__slide .pure-parvus-video'));
        if (!videos.length) {
            activeTrigger = null;
            return;
        }

        // Do not tear the medium out at the start of Parvus' close transition.
        // Fade it with the overlay, then clean up only after that transition has
        // had time to finish. This also keeps Parvus' own helper node intact.
        videos.forEach(video => {
            video.pause();
            video.classList.remove('is-visible');
            video.classList.add('is-closing');
        });

        activeTrigger = null;

        window.setTimeout(() => {
            removeMountedVideos();
        }, 350);
    }

    function mountVideo() {
        const slide = activeSlide();
        if (!slide) return false;

        // Fallback: map Parvus' aria-label ("Image N/X") back to the Nth
        // lightbox trigger in <main>. This also covers keyboard/swipe navigation.
        if (!activeTrigger?.dataset?.parvusVideo) {
            const label = slide.getAttribute('aria-label') || '';
            const match = label.match(/(\d+)\s*\//);
            if (match) {
                const triggers = Array.from(document.querySelectorAll(`main ${selector}`));
                const candidate = triggers[Number(match[1]) - 1];
                if (candidate?.dataset?.parvusVideo) activeTrigger = candidate;
            }
        }

        if (!activeTrigger?.dataset?.parvusVideo) return false;

        const content = slide?.querySelector('.parvus__content');
        if (!slide || !content) return false;

        removeMountedVideos(slide);

        let video = content.querySelector('.pure-parvus-video');
        if (!video) {
            const helper = content.querySelector('img');
            if (helper) {
                helper.classList.add('pure-parvus-video-placeholder', 'is-video-mounted');
            }

            video = document.createElement('video');
            video.className = 'pure-parvus-video';
            video.src = activeTrigger.dataset.parvusVideo;

            const poster = activeTrigger.dataset.parvusVideoPoster;
            if (poster) video.poster = poster;

            video.autoplay = boolData(activeTrigger, 'parvusVideoAutoplay');
            video.controls = boolData(activeTrigger, 'parvusVideoControls');
            video.loop = boolData(activeTrigger, 'parvusVideoLoop');
            video.muted = boolData(activeTrigger, 'parvusVideoMuted');
            video.playsInline = boolData(activeTrigger, 'parvusVideoPlaysinline');
            video.preload = 'metadata';

            // Mirror the configured Pure Video frame in the lightbox.
            // object-fit/object-position only create the same crop when the
            // lightbox video also has the block's configured aspect ratio.
            const objectFit = activeTrigger.dataset.parvusVideoObjectFit || 'contain';
            const objectPosition = activeTrigger.dataset.parvusVideoObjectPosition || 'center center';
            const frameWidth = Number(activeTrigger.dataset.parvusVideoWidth);
            const frameHeight = Number(activeTrigger.dataset.parvusVideoHeight);

            video.style.objectFit = objectFit;
            video.style.objectPosition = objectPosition;

            if (frameWidth > 0 && frameHeight > 0) {
                const ratio = frameWidth / frameHeight;
                video.style.aspectRatio = `${frameWidth} / ${frameHeight}`;
                video.style.width = `min(calc(100vw - 2rem), calc((100svh - 5rem) * ${ratio}))`;
                video.style.height = 'auto';
            }

            content.appendChild(video);
        }

        requestAnimationFrame(() => {
            video.classList.add('is-visible');
            if (video.autoplay) video.play().catch(() => {});
        });

        return true;
    }

    function sync() {
        if (mountVideo()) return;
        requestAnimationFrame(mountVideo);
    }

    function observe() {
        observer?.disconnect();
        const root = document.querySelector('.parvus');
        if (!root) return;
        observer = new MutationObserver(sync);
        observer.observe(root, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['aria-hidden']
        });
    }

    function init() {
        const desktop = window.innerWidth >= 992;

        if (desktop && !prvs) {
            prvs = new Parvus({
                selector,
                // Triggers already carry their explicit data-group.
                // Do not also group via <main>: Parvus would register the same
                // trigger once in the gallery pass and again in its selector pass.
                gallerySelector: null,
                captions: true,
                captionsSelector: 'self',
                captionsAttribute: 'data-parvus-caption',
                docClose: false,
                swipeClose: true,
                simulateTouch: true,
                threshold: 100,
                hideScrollbar: true
            });


            observe();

            prvs.on('open', event => {
                const source = eventSource(event);
                if (source?.dataset?.parvusVideo) activeTrigger = source;
                sync();
            });

            prvs.on('select', event => {
                const source = eventSource(event);
                activeTrigger = source?.dataset?.parvusVideo ? source : null;
                removeMountedVideos();
                sync();
            });

            prvs.on('close', () => {
                finishVideoClose();
            });
        } else if (!desktop && prvs) {
            observer?.disconnect();
            observer = null;
            removeMountedVideos();
            prvs.destroy();
            prvs = null;
            activeTrigger = null;
        }
    }

    init();
    window.addEventListener('resize', init);
});
