/**
 * Parvus
 *
 * @author Benjamin de Oostfrees
 * @version 3.0.0
 * @url https://github.com/deoostfrees/parvus
 *
 * MIT license
 */
document.addEventListener("DOMContentLoaded", () => {
    let prvs = null;

    function initParvus() {
        const isDesktop = window.innerWidth >= 992;

        if (isDesktop && !prvs) {
            // Initialize Parvus on desktop lightbox images
            prvs = new Parvus({
                selector: '.pure-image-desktop a.lightbox',
                gallerySelector: 'main', // could also be a parent container
                captions: true,
                captionsSelector: 'self',
                captionsAttribute: 'data-parvus-caption',
                docClose: false,
                swipeClose: true,
                simulateTouch: true,
                threshold: 100,
                hideScrollbar: true
            });

            // Optionally add dynamically added images
            const newImage = document.querySelector('.lightbox-new');
            if (newImage) prvs.add(newImage);

            // Events
            const EVENT_EL = document.querySelector('.event');
            prvs.on('open', () => {
                if (EVENT_EL) {
                    EVENT_EL.removeAttribute('hidden');
                    EVENT_EL.innerHTML = 'Opened Parvus';
                }
            });
            prvs.on('select', () => {
                if (EVENT_EL) EVENT_EL.innerHTML = `Select slide ${prvs.currentIndex() + 1}`;
            });
            prvs.on('close', () => {
                if (EVENT_EL) {
                    EVENT_EL.innerHTML = 'Closed Parvus';
                    setTimeout(() => EVENT_EL.setAttribute('hidden', true), 2000);
                }
            });
        } else if (!isDesktop && prvs) {
            // Destroy Parvus on mobile
            prvs.destroy();
            prvs = null;
        }
    }

    // Init on load
    initParvus();

    // Re-init on resize
    window.addEventListener('resize', () => {
        initParvus();
    });
});
