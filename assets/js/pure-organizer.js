(function () {
    const DESKTOP_MIN = 992;

    const parseNumber = (value, fallback = 0) => {
        const number = Number.parseFloat(value);
        return Number.isFinite(number) ? number : fallback;
    };

    const visibleItems = (grid) => Array.from(
        grid.querySelectorAll('.pure-organizer-item')
    ).filter(item => item.style.display !== 'none' && !item.hidden);

    const resetItem = (item) => {
        item.style.position = '';
        item.style.left = '';
        item.style.top = '';
        item.style.width = '';
    };

    const itemSpan = (item) => {
        const span = Number.parseInt(item.dataset.span || '12', 10);
        return Math.max(1, Math.min(12, Number.isFinite(span) ? span : 12));
    };

    const itemWidth = (span, scale, columnWidth, gutterX, containerWidth) => {
        const natural = (columnWidth * span) + (gutterX * Math.max(0, span - 1));
        return Math.min(containerWidth, Math.max(columnWidth, natural * scale));
    };

    const measureHeight = (item, width) => {
        item.style.position = 'absolute';
        item.style.width = `${width}px`;
        item.style.left = '0';
        item.style.top = '0';
        return item.getBoundingClientRect().height;
    };

    const layoutGrid = (grid, items, options) => {
        const { containerWidth, columnWidth, gutterX, gutterY, scale } = options;
        let x = 0;
        let y = 0;
        let rowHeight = 0;

        items.forEach(item => {
            const width = itemWidth(itemSpan(item), scale, columnWidth, gutterX, containerWidth);
            const height = measureHeight(item, width);

            if (x > 0 && x + width > containerWidth + 0.5) {
                x = 0;
                y += rowHeight + gutterY;
                rowHeight = 0;
            }

            item.style.left = `${x}px`;
            item.style.top = `${y}px`;

            x += width + gutterX;
            rowHeight = Math.max(rowHeight, height);
        });

        grid.style.height = items.length ? `${y + rowHeight}px` : '0px';
    };

    const layoutMasonry = (grid, items, options) => {
        const { containerWidth, columnWidth, gutterX, gutterY, scale } = options;
        const placed = [];
        const epsilon = 0.5;

        const overlapsHorizontally = (x, width, rect) => {
            return (x < rect.x + rect.width + gutterX - epsilon)
                && (x + width + gutterX > rect.x + epsilon);
        };

        const candidatePositions = (width) => {
            const candidates = [0];

            placed.forEach(rect => {
                // Directly to the right of an existing item.
                candidates.push(rect.x + rect.width + gutterX);

                // Directly to the left of an existing item. This also lets
                // smaller items slide into gaps instead of snapping to a grid.
                candidates.push(rect.x - width - gutterX);
            });

            return Array.from(new Set(
                candidates
                    .filter(x => x >= -epsilon && x + width <= containerWidth + epsilon)
                    .map(x => Math.max(0, Math.min(containerWidth - width, x)))
                    .map(x => Math.round(x * 1000) / 1000)
            )).sort((a, b) => a - b);
        };

        items.forEach(item => {
            const width = itemWidth(itemSpan(item), scale, columnWidth, gutterX, containerWidth);
            const height = measureHeight(item, width);
            const candidates = candidatePositions(width);

            let bestX = 0;
            let bestY = Infinity;

            candidates.forEach(x => {
                let y = 0;

                placed.forEach(rect => {
                    if (overlapsHorizontally(x, width, rect)) {
                        y = Math.max(y, rect.y + rect.height + gutterY);
                    }
                });

                // Prefer the highest available position; for equal heights,
                // keep the natural left-to-right reading order.
                if (y < bestY - epsilon || (Math.abs(y - bestY) <= epsilon && x < bestX)) {
                    bestX = x;
                    bestY = y;
                }
            });

            const x = Number.isFinite(bestX) ? bestX : 0;
            const y = Number.isFinite(bestY) ? bestY : 0;

            item.style.left = `${x}px`;
            item.style.top = `${y}px`;

            placed.push({ x, y, width, height });
        });

        const height = placed.reduce(
            (max, rect) => Math.max(max, rect.y + rect.height),
            0
        );

        grid.style.height = `${Math.max(0, height)}px`;
    };

    const layout = (grid) => {
        if (!grid) return;

        const items = visibleItems(grid);
        const styles = getComputedStyle(grid);
        const gap = parseNumber(styles.columnGap || styles.gap, 0);
        const gutterX = gap;
        const gutterY = gap;
        const scale = parseNumber(grid.style.getPropertyValue('--scale'), 1) || 1;

        if (window.innerWidth < DESKTOP_MIN) {
            grid.style.height = '';
            items.forEach(resetItem);
            grid.classList.add('is-layout-ready');
            return;
        }

        const containerWidth = grid.clientWidth;
        if (!containerWidth) return;

        const columnWidth = (containerWidth - (11 * gutterX)) / 12;
        const options = { containerWidth, columnWidth, gutterX, gutterY, scale };

        if (grid.dataset.layout === 'masonry') {
            layoutMasonry(grid, items, options);
        } else {
            layoutGrid(grid, items, options);
        }

        grid.classList.add('is-layout-ready');
    };

    const scheduleLayout = (grid) => {
        cancelAnimationFrame(grid._pureOrganizerFrame);
        grid._pureOrganizerFrame = requestAnimationFrame(() => layout(grid));
    };

    const bindMediaRelayout = (grid) => {
        grid.querySelectorAll('img').forEach(img => {
            img.addEventListener('load', () => scheduleLayout(grid));
            img.addEventListener('error', () => scheduleLayout(grid));

            if (img.complete && typeof img.decode === 'function') {
                img.decode().catch(() => undefined).then(() => scheduleLayout(grid));
            }
        });

        grid.querySelectorAll('video').forEach(video => {
            ['loadedmetadata', 'loadeddata', 'canplay', 'error'].forEach(eventName => {
                video.addEventListener(eventName, () => scheduleLayout(grid));
            });
        });

        grid.querySelectorAll('iframe').forEach(iframe => {
            iframe.addEventListener('load', () => scheduleLayout(grid));
        });
    };

    const waitForInitialVideoGeometry = (grid) => {
        const itemsInInitialViewport = visibleItems(grid).filter(item => {
            const rect = item.getBoundingClientRect();
            return rect.top < window.innerHeight && rect.bottom > 0;
        });

        const videos = itemsInInitialViewport.flatMap(item =>
            Array.from(item.querySelectorAll('video'))
        );

        const pendingVideos = videos.filter(video =>
            !(video.readyState >= 1 && video.videoWidth > 0 && video.videoHeight > 0)
        );

        if (!pendingVideos.length) {
            return Promise.resolve();
        }

        const waitForVideo = (video) => new Promise(resolve => {
            let settled = false;

            const finish = () => {
                if (settled) return;
                settled = true;
                video.removeEventListener('loadedmetadata', finish);
                video.removeEventListener('error', finish);
                resolve();
            };

            video.addEventListener('loadedmetadata', finish, { once: true });
            video.addEventListener('error', finish, { once: true });

            if (video.readyState >= 1) finish();
        });

        return Promise.race([
            Promise.all(pendingVideos.map(waitForVideo)),
            // Never hold the page indefinitely if a video cannot provide
            // metadata (offline source, stalled request, unsupported file).
            new Promise(resolve => window.setTimeout(resolve, 1500)),
        ]).then(() => new Promise(resolve => {
            // Media events schedule organizer relayouts via requestAnimationFrame.
            // Let those finish, then commit one final synchronous geometry pass.
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    layout(grid);
                    resolve();
                });
            });
        }));
    };

    const initGrid = (grid, index) => {
        if (grid.dataset.pureOrganizerReady === 'true') return Promise.resolve();
        grid.dataset.pureOrganizerReady = 'true';

        const scope = grid.closest('.thumbnail-scope') || grid.parentElement || document;
        const organizerId = grid.dataset.organizerId || grid.dataset.gridId || String(index);
        const scaleInput = scope.querySelector(
            `.pure-organizer-scale[data-organizer-scale-for="${CSS.escape(organizerId)}"]`
        );

        if (scaleInput) {
            const storageKey = `organizerScale-${organizerId}`;
            const savedScale = sessionStorage.getItem(storageKey);

            if (savedScale !== null) scaleInput.value = savedScale;

            const updateScale = () => {
                grid.style.setProperty('--scale', scaleInput.value);
                sessionStorage.setItem(storageKey, scaleInput.value);
                scheduleLayout(grid);
            };

            scaleInput.addEventListener('input', updateScale);
            updateScale();
        } else {
            grid.style.setProperty('--scale', '1');
        }

        const observer = new ResizeObserver(() => scheduleLayout(grid));
        observer.observe(grid);
        visibleItems(grid).forEach(item => observer.observe(item));

        bindMediaRelayout(grid);

        // The first layout must be synchronous. Pure's on-scroll observer is
        // also initialized on DOMContentLoaded and its IntersectionObserver
        // callbacks run asynchronously. Finishing the organizer geometry here
        // ensures those callbacks see the final visual positions instead of
        // the pre-layout document flow.
        layout(grid);

        return waitForInitialVideoGeometry(grid);
    };

    const initAccordions = () => {
        document.querySelectorAll('.pure-organizer-item-list-item.has-content').forEach(item => {
            const header = item.querySelector('.pure-organizer-item-list-item-header');
            if (!header) return;

            header.addEventListener('click', e => {
                if (header.querySelector('a') && e.target.closest('a')) e.preventDefault();
                item.classList.toggle('is-open');
            });

            header.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    item.classList.toggle('is-open');
                }
            });
        });
    };

    let resolveInitialReady;

    const initialReady = new Promise(resolve => {
        resolveInitialReady = resolve;
    });

    window.PureOrganizer = {
        layout,
        relayout: scheduleLayout,
        initialReady,
    };

    document.addEventListener('DOMContentLoaded', () => {
        initAccordions();

        const grids = Array.from(
            document.querySelectorAll('.pure-organizer[data-layout]')
        );

        Promise.all(grids.map(initGrid))
            .catch(() => undefined)
            .then(() => resolveInitialReady());
    });
})();
