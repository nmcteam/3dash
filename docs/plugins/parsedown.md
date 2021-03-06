# 3dash Parsedown Plugin

The Parsedown plugin converts [content file](../files.md#content-files) 
body content from Markdown into HTML using the popular [Parsedown](https://parsedown.org/)
parser. This only applies to content files with a `.md` or `.markdown` 
file extension.

After this plugin runs, affected content files' `body` properties
will be HTML instead of Markdown.

## System requirements

This plugin requires the `erusev/parsedown` package. Install it with Composer:

```
composer require erusev/parsedown
```

## Use the plugin

To use this plugin, add an instance of the `Parsedown` class to your
3dash app like this:

```
$app->add(new Ssg\Body\Parsedown());
```
