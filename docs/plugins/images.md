## 3dash Images Plugin

The Images plugin provides an image resizer method in the 
[payload](../payload.md) object's `site` property.

## System requirements

This plugin requires the `intervention/image` package. Install it with Composer:

```
composer require intervention/image
```

## Configuration

To use this plugin, add a `Images` class instance to your app:

```
$app->add(new Images());
```

## How to resize images

This plugin provides a `resize()` method to resize images.

```
$payload->site['images']->resize($path, $transforms);
```

The `resize()` method accepts two arguments:

* `path` - String. A valid array key (i.e. asset file path) in `$payload->assets`;
* `transforms` - Array. Image transformations.

## Image transformations

The `transforms` array may use these keys:

* `width` - Integer. Required. Resized image width in pixels.
* `height` - Integer. Optional. Resized image height in pixels.
* `fit` - String. Optional. One of: `cover`, `contain`.
* `format` - String. Optional. One of: `jpg`, `png`, `gif`, `webp`.
* `grayscale` - Boolean. Optional.
* `quality` - Integer. Optional. Resized image quality: 0-100 (inclusive).
* `sharpen` - Integer. Optional. Resized image sharpening: 0-100 (inclusive).

## Twig integration

If you add the Images plugin _before_ the Twig plugin, you may create resized 
images at render-time in Twig templates like this:

```
{% set src = site.images.resize("/path/to/image.jpg", {
    format: "webp",
    width: 640,
    height: 480,
    fit: cover
}) %}
<img src="{{ src }}" alt="My image"/>
```
