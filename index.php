<?php

Kirby::plugin('kirbypure/blocks', [
    'blueprints' => [

        // Pages
        'pages/pure-page' =>
            __DIR__ . '/blueprints/pages/pure-page.yml',

        'pages/pure-subpage' =>
            __DIR__ . '/blueprints/pages/pure-subpage.yml',

        // Blocks

        'blocks/pure-image' =>
            __DIR__ . '/blueprints/blocks/pure-image.yml',

        'blocks/pure-flexible-text' =>
            __DIR__ . '/blueprints/blocks/pure-flexible-text.yml',

        'blocks/pure-organizer' =>
            __DIR__ . '/blueprints/blocks/pure-organizer.yml',

        'blocks/pure-spacer' =>
            __DIR__ . '/blueprints/blocks/pure-spacer.yml',

        'blocks/pure-swiper' =>
            __DIR__ . '/blueprints/blocks/pure-swiper.yml',

        'blocks/pure-video' =>
            __DIR__ . '/blueprints/blocks/pure-video.yml',

        // Navigation blocks
        'blocks/pure-site-header-navigation' =>
            __DIR__ . '/blueprints/blocks/pure-site-header-navigation.yml',

        'blocks/pure-site-footer-navigation' =>
            __DIR__ . '/blueprints/blocks/pure-site-footer-navigation.yml',

        // Shared fields

        'fields/anchor' =>
            __DIR__ . '/blueprints/fields/anchor.yml',

        'fields/corners' =>
            __DIR__ . '/blueprints/fields/corners.yml',

        'fields/gap' =>
            __DIR__ . '/blueprints/fields/gap.yml',

        'fields/links' =>
            __DIR__ . '/blueprints/fields/links.yml',

        'fields/space-after' =>
            __DIR__ . '/blueprints/fields/space-after.yml',

        'fields/text-align' =>
            __DIR__ . '/blueprints/fields/text-align.yml',

        // Groups
        'groups/page-builder' =>
            __DIR__ . '/blueprints/groups/page-builder.yml',

        // Sections
        'sections/pure-organizer-metadata' =>
            __DIR__ . '/blueprints/sections/pure-organizer-metadata.yml',

        // Tabs
        'tabs/pure-navigation' =>
            __DIR__ . '/blueprints/tabs/pure-navigation.yml',

        'tabs/pure-blocks-settings' =>
            __DIR__ . '/blueprints/tabs/pure-blocks-settings.yml',
    ],

    'snippets' => [
        // Site structure
        'pure-site-header' =>
            __DIR__ . '/snippets/pure-site-header.php',

        'pure-site-footer' =>
            __DIR__ . '/snippets/pure-site-footer.php',

        // Block-specific variables
        'pure-blocks-variables' =>
            __DIR__ . '/snippets/pure-blocks-variables.php',

        // Rendering infrastructure
        'pure-blocks' =>
            __DIR__ . '/snippets/pure-blocks.php',

        'pure-blocks-wrapper' =>
            __DIR__ . '/snippets/pure-blocks-wrapper.php',

        'pure-layout' =>
            __DIR__ . '/snippets/pure-layout.php',

        // Blocks

        'blocks/pure-image' =>
            __DIR__ . '/snippets/blocks/pure-image.php',

        'blocks/pure-flexible-text' =>
            __DIR__ . '/snippets/blocks/pure-flexible-text.php',

        'blocks/pure-organizer' =>
            __DIR__ . '/snippets/blocks/pure-organizer.php',

        'blocks/pure-spacer' =>
            __DIR__ . '/snippets/blocks/pure-spacer.php',

        'blocks/pure-swiper' =>
            __DIR__ . '/snippets/blocks/pure-swiper.php',

        'blocks/pure-video' =>
            __DIR__ . '/snippets/blocks/pure-video.php',

        // Navigation blocks
        'blocks/pure-site-header-navigation' =>
            __DIR__ . '/snippets/blocks/pure-site-header-navigation.php',

        'blocks/pure-site-footer-navigation' =>
            __DIR__ . '/snippets/blocks/pure-site-footer-navigation.php',

        // Organizer
        'organizer/filter-buttons' =>
            __DIR__ . '/snippets/organizer/filter-buttons.php',

        'organizer/scale' =>
            __DIR__ . '/snippets/organizer/scale.php',

        'organizer/thumbnail' =>
            __DIR__ . '/snippets/organizer/thumbnail.php',

        // Render helpers
        'render/pure-image' =>
            __DIR__ . '/snippets/render/pure-image.php',

        'render/pure-video' =>
            __DIR__ . '/snippets/render/pure-video.php',

        'render/pure-links' =>
            __DIR__ . '/snippets/render/pure-links.php',
    ],

    'hooks' => [
        'page.render:after' => function (
            string $contentType,
            array $data,
            string $html
        ) {
            /*
             * Vendor CSS
             *
             * Loaded at the beginning of <head> so Pure variables
             * and Pure overrides can overwrite vendor defaults.
             */
            $vendorStylesheets = [
                '/media/plugins/kirbypure/blocks/css/parvus.min.css',
                '/media/plugins/kirbypure/blocks/css/swiper-bundle.min.css',
            ];

            /*
             * Pure Blocks CSS
             */
            $stylesheets = [
                '/media/plugins/kirbypure/blocks/css/pure-navigation.css',
                '/media/plugins/kirbypure/blocks/css/pure-organizer.css',
                '/media/plugins/kirbypure/blocks/css/pure-blocks.css',
                '/media/plugins/kirbypure/blocks/css/pure-parvus.css',
                '/media/plugins/kirbypure/blocks/css/pure-swiper.css',
            ];

            $vendorCss = '';
            $css = '';

            foreach ($vendorStylesheets as $path) {
                $vendorCss .= class_exists('Bnomei\\Fingerprint')
                    ? Bnomei\Fingerprint::css($path)
                    : css($path);

                $vendorCss .= PHP_EOL;
            }

            foreach ($stylesheets as $path) {
                $css .= class_exists('Bnomei\\Fingerprint')
                    ? Bnomei\Fingerprint::css($path)
                    : css($path);

                $css .= PHP_EOL;
            }

            // Vendor CSS first.
            $html = str_replace(
                '<head>',
                '<head>' . PHP_EOL . $vendorCss,
                $html
            );

            // Pure Blocks CSS last.
            $html = str_replace(
                '</head>',
                $css . '</head>',
                $html
            );

            /*
             * JavaScript
             *
             * Vendor libraries must be loaded before the Pure
             * scripts that depend on them.
             */
            $javascript = [

                // Vendor libraries
                '/media/plugins/kirbypure/blocks/js/parvus.min.js',
                '/media/plugins/kirbypure/blocks/js/swiper-bundle.min.js',

                // Pure Blocks
                '/media/plugins/kirbypure/blocks/js/pure-navigation.js',
                '/media/plugins/kirbypure/blocks/js/pure-header-height.js',
                '/media/plugins/kirbypure/blocks/js/pure-organizer.js',
                '/media/plugins/kirbypure/blocks/js/pure-parvus.js',
                '/media/plugins/kirbypure/blocks/js/pure-swiper.js',
                '/media/plugins/kirbypure/blocks/js/pure-video.js',
            ];

            $js = '';

            foreach ($javascript as $path) {
                $js .= class_exists('Bnomei\\Fingerprint')
                    ? Bnomei\Fingerprint::js($path)
                    : js($path);

                $js .= PHP_EOL;
            }

            /*
             * Unlazy
             *
             * Keep the original `defer init` behavior.
             * It is therefore loaded separately from the normal JS loop.
             */
            $unlazyUrl = url(
                'media/plugins/kirbypure/blocks/js/unlazy.with-hashing.iife.js'
            );

            $unlazy =
                '<script src="' .
                htmlspecialchars($unlazyUrl, ENT_QUOTES, 'UTF-8') .
                '" defer init></script>';

            $html = str_replace(
                '</body>',
                $unlazy . PHP_EOL .
                $js .
                '</body>',
                $html
            );

            return $html;
        }
    ],

    'api' => [
        'routes' => [
            [
                'pattern' => 'image-preview',
                'method'  => 'GET',

                'action' => function () {
                    $id = (string)$this->requestQuery('id', '');
                    $ratio = (string)$this->requestQuery('ratio', '');

                    if ($id === '') {
                        return ['url' => null];
                    }

                    $file = $this->kirby()->file($id);

                    if (!$file || $file->type() !== 'image') {
                        return ['url' => null];
                    }

                    if ($ratio === '' || preg_match('~^(\d+(?:\.\d+)?)/(\d+(?:\.\d+)?)$~', $ratio, $matches) !== 1) {
                        return ['url' => $file->url()];
                    }

                    $ratioWidth = (float)$matches[1];
                    $ratioHeight = (float)$matches[2];

                    if ($ratioWidth <= 0 || $ratioHeight <= 0) {
                        return ['url' => $file->url()];
                    }

                    $width = 2400;
                    $height = (int)round($width * $ratioHeight / $ratioWidth);

                    try {
                        $thumb = $file->thumb([
                            'width'   => $width,
                            'height'  => $height,
                            'sharpen' => 25,
                            'crop'    => true,
                            'format'  => 'webp',
                            'quality' => 75,
                        ]);

                        return ['url' => $thumb->url()];
                    } catch (Throwable $e) {
                        return ['url' => $file->url()];
                    }
                },
            ],
            [
                'pattern' => 'organizer-preview/(:all)',
                'method'  => 'GET',

                'action' => function (string $source = 'site') {
                    $source = urldecode($source);

                    if ($source === 'site') {
                        $pages = site()->index();
                    } else {
                        $sourceId = str_replace('+', '/', $source);
                        $sourcePage = page($sourceId);

                        if (!$sourcePage) {
                            return ['items' => []];
                        }

                        $pages = $sourcePage->children();
                    }

                    $items = $pages
                        ->listed()
                        ->filter(fn ($page) => $page->intendedTemplate()->name() === 'pure-subpage')
                        ->map(function ($page) {
                            $tags = array_values(array_filter(array_map(
                                'trim',
                                explode(',', (string)$page->tags())
                            )));

                            $thumbnail = null;

                            if ($page->thumbnail()->isNotEmpty()) {
                                foreach ($page->thumbnail()->toBlocks() as $thumbBlock) {
                                    $type = $thumbBlock->type();
                                    $content = $thumbBlock->content();

                                    if (in_array($type, ['pure-image', 'image'], true)) {
                                        $file = $content->get('image')->toFile();

                                        if (!$file) {
                                            continue;
                                        }

                                        $borderColor = $content->get('bordercolor')->value();
                                        $crop = $content->get('crop')->toBool();
                                        $ratio = $content->get('ratio')->value();
                                        $thumbnailUrl = $file->thumb(['width' => 1200])->url();

                                        // Use Kirby's real crop for Organizer previews as well.
                                        // This mirrors the frontend image renderer, including the
                                        // file's saved focus point, instead of approximating the crop
                                        // with CSS object-position in Vue.
                                        $extension = strtolower($file->extension());
                                        $canGenerateThumbs = !in_array($extension, ['gif', 'svg'], true);

                                        if ($crop && $ratio && $canGenerateThumbs && preg_match('~^(\d+(?:\.\d+)?)/(\d+(?:\.\d+)?)$~', $ratio, $matches) === 1) {
                                            $ratioWidth = (float)$matches[1];
                                            $ratioHeight = (float)$matches[2];

                                            if ($ratioWidth > 0 && $ratioHeight > 0) {
                                                $thumbWidth = 1200;
                                                $thumbHeight = (int)round($thumbWidth * $ratioHeight / $ratioWidth);

                                                try {
                                                    $thumbnailUrl = $file->thumb([
                                                        'width'   => $thumbWidth,
                                                        'height'  => $thumbHeight,
                                                        'sharpen' => 25,
                                                        'crop'    => true,
                                                        'format'  => 'webp',
                                                        'quality' => 75,
                                                    ])->url();
                                                } catch (Throwable $e) {
                                                    // Keep the uncropped preview fallback above.
                                                }
                                            }
                                        }

                                        $thumbnail = [
                                            'type'        => 'image',
                                            'url'         => $thumbnailUrl,
                                            'alt'         => $content->get('alt')->or($file->alt())->value(),
                                            'crop'        => $crop,
                                            'ratio'       => $ratio,
                                            'corners'     => $content->get('corners')->value(),
                                            'border'      => $content->get('border')->toBool(),
                                            'borderColor' => $borderColor ?: null,
                                        ];

                                        break;
                                    }

                                    if ($type === 'pure-video') {
                                        $video = $content->get('video')->toFile();
                                        $poster = $content->get('poster')->toFile();
                                        $externalUrl = $content->get('url')->value();

                                        if (!$video && !$poster && !$externalUrl) {
                                            continue;
                                        }

                                        $thumbnail = [
                                            'type'     => 'video',
                                            'url'      => $video?->url(),
                                            'poster'   => $poster?->thumb(['width' => 1200])->url(),
                                            'external' => $externalUrl ?: null,
                                            'width'    => $content->get('width')->toInt(),
                                            'height'   => $content->get('height')->toInt(),
                                            'corners'  => $content->get('corners')->value(),
                                        ];

                                        break;
                                    }

                                    if ($type === 'pure-swiper') {
                                        $slides = [];

                                        foreach ($content->get('images')->toFiles() as $file) {
                                            $isVideo = $file->type() === 'video';
                                            $slides[] = [
                                                'type' => $isVideo ? 'video' : 'image',
                                                'url'  => $isVideo
                                                    ? $file->url()
                                                    : $file->thumb(['width' => 1200])->url(),
                                                'alt'  => $isVideo ? '' : $file->alt()->value(),
                                            ];
                                        }

                                        if (!$slides) {
                                            continue;
                                        }

                                        $thumbnail = [
                                            'type'        => 'swiper',
                                            'slides'      => $slides,
                                            'aspectRatio' => $content->get('aspectratio')->or('auto')->value(),
                                            'imageFit'    => $content->get('imagefit')->or('cover')->value(),
                                        ];

                                        break;
                                    }
                                }
                            }

                            $year = $page->date()->isNotEmpty()
                                ? $page->date()->toDate('Y')
                                : null;

                            return [
                                'id'             => $page->id(),
                                'title'          => $page->title()->value(),
                                'thumbnail'      => $thumbnail,
                                'thumbnailWidth' => max(1, min(12, $page->thumbnailWidth()->or(6)->toInt())),
                                'date'           => $page->date()->value(),
                                'year'           => $year,
                                'tags'           => $tags,
                            ];
                        })
                        ->values();

                    return ['items' => $items];
                }
            ],
            [
                'pattern' => 'navigation-preview',
                'method'  => 'GET',

                'action' => function () {
                    $site = site();

                    return [
                        'title' => $site->title()->value(),

                        'pages' => $site
                            ->children()
                            ->listed()
                            ->map(fn ($page) => [
                                'id'    => $page->id(),
                                'title' => $page->title()->value(),
                            ])
                            ->values(),
                    ];
                }
            ]
        ]
    ]
]);
