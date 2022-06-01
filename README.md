# NMC Static Site Generator

This is a tiny static site generator. The concept is simple. There is a "payload" 
object; its keys are pathnames, and its values are `File` instances. The payload is
passed through a sequence of plugins. The resultant payload's keys represent corresponding
files in the output build directory, and the payload's `File` values represent the content of
those files.

## Payload

The payload is a `\stdObject` instance with these properties:

1. `root` - A `\SplFileInfo` object for the directory containing site files;
2. `files` - Associative array. Keys are pathnames beneath `root`, and values are `File` instances;
3. `assets` - Associative array. Keys are pathnames beneath `root`, and values are `File` instances;
4. `site` - Associative array of site metadata;

The first plugin indexes all site files beneath `root`.

_Content_ files are parsed for header metadata and body content. They are available in the payload 
`files` property. By default, files with these extensions are considered _content_ files:

* html
* md
* markdown
* txt
* xml
* rss

All other files are considered _asset_ files. They are not parsed, and they are copied verbatim to 
the output directory. Assets are available in the payload `assets` property.

## Plugins

A plugin is any class instance that implements the `PluginInterface` interface:

```
public function handle(object $payload);
```
