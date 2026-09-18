# Kirby Pure Blocks

> **Experimental Early Alpha Release**

### About

`Kirby Pure Blocks` extends `Kirby Pure` with reusable blocks, page-building infrastructure, navigation, and shared media renderers.

Universal Renders:
- Pure Image
- Pure Video
- Pure Links

Blocks that utilize the renders:
- Pure Image
- Pure Video
- Pure Swiper
- Pure Site Header Navigation
- Pure Site Footer Navigation

Additional Blocks:
- Organizer (A filterable page collection that shows thumbnails in a grid)
- Flexible Text
- Spacer

## Requirements

- Kirby 5
- [Kirby Pure](https://github.com/felix-rabe/kirby-pure)
- [Tobimori ThumbHash](https://github.com/tobimori/kirby-thumbhash)

> This plugin is intended to work with `Kirby Pure`. Please check out the Repository [here](https://github.com/felix-rabe/kirby-pure) before you continue. 

> `Kirby Pure Blocks` uses `Tobimori ThumbHash` to generate lightweight placeholders for images and video posters. It currently assumes that ThumbHash is available and therefore requires the plugin.

## Installation

Copy this repository to:

```text
site/plugins/kirby-pure-blocks
```

## Integration

### Page Template

Add the Pure Blocks rendering snippets to your template.

```php
<?php
snippet('pure-blocks');
snippet('pure-layout');
?>
```

A typical `site/templates/default.php` can then look like this:

```php
<?php
snippet('pure-header');
snippet('pure-blocks');
snippet('pure-layout');
snippet('pure-footer');
?>
```

`pure-blocks` renders the standard Blocks field.

`pure-layout` renders the Kirby Layout field.

### Site Blueprint

Add the following to your site blueprint:

```yaml
tabs:

  navigation:
    extends: tabs/pure-navigation

  blocksSettings:
    extends: tabs/pure-blocks-settings
```

Project-specific tabs can remain directly in the site blueprint.

### Page Blueprints

Kirby Pure Blocks provides two reusable page blueprints:

```text
pure-page
pure-subpage
```

The project's local `default.yml` can simply extend the Pure Page blueprint:

```yaml
extends: pages/pure-page
```

OR

You can enforce `pure-page` templates directly in your `site.yml`:

```yaml
sections:
  pages:
    type: pages
    template: pure-page
```

`pure-page` templates automatically create child pages using `pure-subpage`, which provides additional Organizer metadata fields for subpages.

### Organizer Metadata

Pages used by the `Organizer-Block` can include the reusable Organizer Metadata section:

```yaml
sidebar:
  width: 1/3
  sections:

    organizerMetadata:
      extends: sections/pure-organizer-metadata
```

The section provides page metadata used by the Organizer:

- Thumbnail
- Thumbnail width
- Date
- Tags

Keeping these fields in Kirby Pure Blocks ensures that Organizer-compatible page blueprints can reuse the same metadata structure across projects.

### Header and Footer

When Kirby Pure Blocks is installed, the `pure-header` and `pure-footer` snippets provided by Kirby Pure automatically include the Pure Blocks site header and footer navigation.

No additional header or footer snippets need to be added to the project template.

## Blocks

### Using Blocks

Out of the box, Pure uses Kirby’s standard blocks. A quick way to make Pure Blocks available throughout your project is via your site’s `config.php`:

```php
// config.php
'blocks' => [
    'fieldsets' => [
        'Media' => [
            'label' => 'Media',
            'type' => 'group',
            'fieldsets' => [
                'pure-image',
                'pure-video',
                'pure-swiper',
                'pure-flexible-text',
            ],
        ],
        'Compositions' => [
            'label' => 'Compositions',
            'type' => 'group',
            'fieldsets' => [
                'pure-organizer',
                'pure-spacer',
            ],
        ],
    ],
],
```

Pure Blocks can also be added to individual Kirby Blocks fields using the `fieldsets` option. Field-specific `fieldsets` override the project-wide configuration:

```text
fieldsets:
  - pure-image
  - pure-video
  - pure-swiper
  - pure-flexible-text
  - pure-organizer
  - pure-spacer
```

### Customization

You can use the provided Pure blocks right away. But if you want to change or optimize something or build custom blocks, keep in mind:

> DO NOT TOUCH THE PURE BLOCKS!

It is recommended to build upon them in your own `your-project` plugin. Instead of rebuilding a block from scratch, a project-specific blueprint can extend an existing Pure block and add only the fields required by the project.

For example, your new `Project Image` block can reuse all fields and functionality of the `Pure Image` block while adding a project-specific field:

~~~yaml
title: Project Image

# Use the Pure Image markup
extends: blocks/pure-image

# Add your Project Image customizations
fields:
  customField:
    label: Custom Field
    type: text
~~~

The project-specific block can continue to use the existing Pure image renderer while incorporating the additional field into its own markup or behavior. This preserves responsive images, ThumbHash placeholders, cropping, links, lightbox support and the other shared functionality without duplicating the underlying implementation.

## Block Rendering

Kirby Pure Blocks provides the rendering infrastructure used by Pure-based page builders.

The main rendering snippets are:

```text
pure-blocks.php
pure-blocks-wrapper.php
pure-layout.php
```

`pure-blocks.php` renders blocks from the standard Blocks field.

`pure-blocks-wrapper.php` provides the common wrapper around individual blocks, including layout and spacing classes.

It also uses an optional block anchor as the `id` of the outer block wrapper.
Kirby's internal block ID remains the fallback when the anchor is empty.

Project-specific block blueprints can reuse the field with:

```yaml
fields:
  anchor:
    extends: fields/anchor
```

Enter the anchor without `#`. A link using the same anchor will then target
the outermost block wrapper, for example `#contact`. The shared field uses
Kirby's slug input and automatically normalizes entries to lowercase,
URL-safe anchor values.

`pure-layout.php` renders Kirby Layout fields and their columns, blocks and layout settings.

Individual block snippets are rendered internally from:

```text
snippets/blocks/
```

Reusable media and link rendering is handled through:

```text
snippets/render/
```

## Navigation

### Pure Site Header Navigation

Provides a configurable site navigation with support for:

- Site title
- Listed pages
- Custom links
- Desktop and mobile navigation
- Mobile menu
- Sticky navigation
- Hide on scroll

### Pure Site Footer Navigation

Provides configurable footer navigation with custom links.

## Organizer

The Organizer provides a way to render and filter collections of child pages in a thumbnail grid. 

Organizer functionality includes support for:

- Tag filtering
- Date filtering
- Multi-select filtering
- Item scaling
- Grid and Masonry layouts
- Dependency-free Organizer layout and filtering
- Optional metadata
- Sticky filter controls

## Media Rendering

### Images

The reusable image renderer provides functionality including:

- Responsive image sources
- WebP generation
- JPEG fallbacks
- ThumbHash placeholders
- Optional cropping and aspect ratios
- Image focus
- Borders and corner styles
- Maximum width and alignment
- Links
- Parvus lightbox integration
- On-scroll transitions

### Video

The reusable video renderer supports:

- Kirby-hosted videos
- External video URLs
- Posters
- ThumbHash placeholders
- Autoplay
- Controls
- Looping
- Muted playback
- Inline playback
- Links
- YouTube and Vimeo consent loading
- On-scroll transitions

## Vendor Dependencies

Kirby Pure Blocks includes frontend libraries used by specific components:

```text
Parvus Lightbox
Swiper
Unlazy
```

Vendor assets are loaded before the corresponding Pure scripts and styles that depend on them.

## Required Dependencies

### Tobimori ThumbHash

Kirby Pure Blocks requires the Tobimori ThumbHash plugin. ThumbHash is used by the reusable image and video rendering infrastructure to generate lightweight placeholders while media is loading. The image renderer directly uses ThumbHash for image placeholders. Video posters can use the same placeholder infrastructure.

### Recommended ThumbHash settings

```php
// config.php

'tobimori.thumbhash' => [
    'sampleMaxSize' => 100,
    'blurRadius' => 3,
],
```
## Structure

```text
kirby-pure-blocks/
├── assets/
│   ├── css/
│   │   ├── pure-navigation.css
│   │   ├── pure-organizer.css
│   │   ├── pure-blocks.css
│   │   ├── pure-parvus.css
│   │   ├── pure-spacer.css
│   │   ├── pure-swiper.css
│   │   └── vendor assets
│   ├── js/
│   │   ├── pure-navigation.js
│   │   ├── pure-header-height.js
│   │   ├── pure-organizer.js
│   │   ├── pure-parvus.js
│   │   ├── pure-swiper.js
│   │   ├── pure-video.js
│   │   └── vendor assets
│   └── screenshots/
│
├── blueprints/
│   ├── blocks/
│   ├── fields/
│   ├── groups/
│   │   └── page-builder.yml
│   ├── pages/
│   │   ├── pure-page.yml
│   │   └── pure-subpage.yml
│   ├── sections/
│   │   └── pure-organizer-metadata.yml
│   └── tabs/
│       ├── pure-navigation.yml
│       └── pure-blocks-settings.yml
│
├── snippets/
│   ├── blocks/
│   ├── organizer/
│   ├── render/
│   ├── pure-blocks.php
│   ├── pure-blocks-variables.php
│   ├── pure-blocks-wrapper.php
│   ├── pure-layout.php
│   ├── pure-site-header.php
│   └── pure-site-footer.php
│
├── src/
│   ├── components/
│   └── index.js
│
├── .gitignore
├── LICENSE
├── composer.json
├── package.json
├── package-lock.json
├── index.css
├── index.js
└── index.php
```

## License

MIT © 2026 Felix Rabe
