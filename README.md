# Kirby Pure Blocks

> **Experimental Early Alpha Release**

### About

`Kirby Pure Blocks` extends `Kirby Pure` with reusable blocks, page-building infrastructure, navigation, and shared image, video and link rendering.

It includes image, video, swiper, flexible text, organizer and spacer blocks as well as basic header and footer navigation.

## Requirements

- Kirby 5
- [Kirby Pure](https://github.com/felix-rabe/kirby-pure)
- [Tobimori ThumbHash](https://github.com/tobimori/kirby-thumbhash)

`Kirby Pure Blocks` is intended to work with `Kirby Pure`.

`Tobimori ThumbHash` is required to generate lightweight placeholders for images and video posters.

## Installation

Copy this repository to:

```text
site/plugins/kirby-pure-blocks
```

## Integration

### Page Template

Add the Pure Blocks rendering snippets to your template:

```php
<?php
snippet('pure-blocks');
snippet('pure-layout');
?>
```

A typical `site/templates/default.php` can look like this:

```php
<?php
snippet('pure-header');
snippet('pure-blocks');
snippet('pure-layout');
snippet('pure-footer');
?>
```

`pure-blocks` renders the standard Kirby Blocks field.

`pure-layout` renders the Kirby Layout field.

### Site Blueprint

Add the Pure Blocks settings and navigation to your site blueprint:

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

Your project's local `default.yml` can extend the Pure Page blueprint:

```yaml
extends: pages/pure-page
```

Alternatively, you can use `pure-page` directly in your `site.yml`:

```yaml
sections:
  pages:
    type: pages
    template: pure-page
```

`pure-page` automatically creates child pages using `pure-subpage`, which provides additional Organizer metadata fields.

### Header and Footer

When Kirby Pure Blocks is installed, the `pure-header` and `pure-footer` snippets provided by Kirby Pure automatically include the Pure Blocks header and footer navigation.

No additional navigation snippets need to be added to the project template.

## Blocks

### Using Blocks

Pure Blocks can be made available throughout your project via your site's `config.php`:

```php
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

Pure Blocks can also be added to individual Kirby Blocks fields:

```yaml
fieldsets:
  - pure-image
  - pure-video
  - pure-swiper
  - pure-flexible-text
  - pure-organizer
  - pure-spacer
```

Field-specific `fieldsets` override the project-wide configuration.

### Customization

> **DO NOT TOUCH THE PURE BLOCKS!**

Project-specific customizations should live in your own project plugin. Existing Pure blocks can be extended instead of duplicated.

For example:

```yaml
title: Project Image

extends: blocks/pure-image

fields:
  customField:
    label: Custom Field
    type: text
```

This keeps the shared Pure functionality intact while allowing project-specific fields and behavior.

### Block Anchors

Blocks can use an optional anchor as the `id` of their outer wrapper. Kirby's internal block ID remains the fallback when no anchor is set.

Project-specific block blueprints can reuse the field with:

```yaml
fields:
  anchor:
    extends: fields/anchor
```

## Organizer

The Organizer renders and filters collections of child pages in a grid or Masonry layout. It supports tags, dates, multi-select filtering, item scaling and optional metadata.

Pages used by the Organizer can include the reusable metadata section:

```yaml
sidebar:
  width: 1/3
  sections:

    organizerMetadata:
      extends: sections/pure-organizer-metadata
```

This provides:

- Thumbnail
- Thumbnail width
- Date
- Tags

## Rendering

Pure Blocks provides shared image, video and link rendering.

Image and video rendering supports responsive images, ThumbHash placeholders, cropping, image focus, links, Parvus lightboxes, video posters, YouTube and Vimeo consent loading, and on-scroll transitions.

Reusable rendering snippets are located in:

```text
snippets/render/
```

Frontend functionality uses the bundled `Parvus`, `Swiper` and `Unlazy` libraries.

Third-party licenses are documented in `THIRD_PARTY_LICENSES`.

### Recommended ThumbHash Settings

```php
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
│   ├── js/
│   └── screenshots/
├── blueprints/
│   ├── blocks/
│   ├── fields/
│   ├── groups/
│   ├── pages/
│   ├── sections/
│   └── tabs/
├── snippets/
│   ├── blocks/
│   ├── organizer/
│   └── render/
├── src/
│   ├── components/
│   └── index.js
├── composer.json
├── package.json
├── index.css
├── index.js
└── index.php
```

## License

MIT © 2026 Felix Rabe