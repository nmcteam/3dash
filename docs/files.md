# 3dash Files

3dash has two types of files: _content_ files and _asset_ files.
Content files are parsed for header properties and body content,
and asset files are often output verbatim.

## Content Files

A _content_ file has two parts: a header and a body. These two parts are 
separated by `---` (three dashes). 3dash. Get it!? Ok. Carrying on. This is 
an example content file:

```
title = "Page title"
date = "2022-05-29"
template = "page.twig"

---

<p>This is the page body</p>
```

The header and body can use whatever formats you want as long as you add plugins 
that can parse them. The header and body are parsed separately, so they do not 
need to use the same format. For example, a header may use INI or YAML, and the 
body may use Markdown, Textile, or even plain HTML.

By default, 3dash considers these file extensions to be content files:

* html
* md
* markdown
* txt
* xml
* rss

If you prefer to _append_ new extensions to this list, you may provide a
custom `Indexer` instance to the 3dash app constructor like this:

```
<?php
require './vendor/autoload.php';

use Nmc\Ssg;

$app = new Ssg\App(
    __DIR__ . '/site/content',
    new Indexer(['haml'])
);
```

## Asset files

An _asset_ file is not parsed. It is often output verbatim into the output directory. 
That being said, plugins do have access to asset files; plugins may add, update, or 
remove asset files as needed.

## The `File` class

3dash uses the `File` class to represent files ultimately generated in the output
directory. Each `File` instance has an associative array of properties
that can be manipulated with this public interface:

```
public function get(string $key): mixed;
public function set(string $key, $value);
public function add(array $props);
public function has(string $key): bool;
public function remove(string $key);
public function all(): array;
```

The `File` class also implements the `\ArrayAccess` interface
that composes on the above methods.

File properties can be whatever you want. However, the `body`
property is special; it is always a `\Psr\Http\Message\StreamInterface`
instance.

The `File` class also provides these static constructors:

```
public static function fromString(string $input, array $props = []);
public static function fromResource(resource $resource, array $props = []);
public static function fromFile(string $pathname, array $props = [])
```

These static constructors create `File` instances from strings, PHP resources,
and file pathnames, respectively.

If you use the `set()` method or the `[] =` array assignment operator to
set the `body` property directly, 3dash will to convert your value to a 
valid StreamInterface instance using the `\GuzzleHttp\Psr7\Utils\streamFor()`
function; an Exception is thrown if the conversion fails.