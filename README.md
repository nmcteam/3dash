# NMC Static Site Generator

This is a tiny static site generator created as a weekend project. I wanted a simple
PHP solution, similar to Metalsmith, to create a tiny personal blog.

The concept is simple. There is a "payload" object; its `files` and `assets` properties
are associative arrays whose keys are pathnames and whose values are `File` instances. 
The payload is passed through a sequence of plugins. The resultant payload's `files`
and `assets` arrays represent corresponding files in the output build directory.

## Payload

The payload is a `\stdObject` instance with these properties:

1. `root` - A `\SplFileInfo` object for the directory containing site files;
2. `files` - Associative array. Keys are pathnames beneath `root`, and values are `File` instances;
3. `assets` - Associative array. Keys are pathnames beneath `root`, and values are `File` instances;
4. `site` - Associative array of site metadata;

The first plugin always indexes site files beneath `root`. Subsequent plugins manipulate those files.

_Content_ files are parsed for header metadata and body content. Content files are 
available in the payload's `files` property. By default, files with these extensions 
are considered _content_ files:

* html
* md
* markdown
* txt
* xml
* rss

All other files are considered _asset_ files. They are not parsed, and they are copied verbatim into 
the output directory. Asset files are available in the payload's `assets` property.

## Content files

A _content_ file has two parts: a header and a body. These two parts are separated by `---` (three dashes).

```
title = "Page title"
date = "2022-05-29"
template = "page.twig"

---

<p>This is the page body</p>
```

The header format can be whatever you want as long as you use a plugin that can parse it. This repo
provides a `Header\Ini` class to support the INI header format. The header must be parseable
into an associative array; this array's keys and values are available on the corresponding 
`File` instance via the `\ArrayAccess` interface.

The body can also be whatever you want. Its content is available in the corresponding `File`
instance's `body` property. A _content_ file's `body` property is the content of the output file.
This repo provides a `Body\Parsedown` class to support Markdown body content, converted with Parsedown.

## Plugins

A plugin is any class instance that implements the `PluginInterface` interface:

```
public function handle(object $payload);
```
