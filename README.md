# NMC Static Site Generator

This is a tiny static site generator created as a weekend project. I wanted a simple
PHP solution, similar to Metalsmith, to create a tiny personal blog.

The concept is simple. There is a "payload" object with several properties, including
`files` and `assets`. These properties are associative arrays whose keys are pathnames 
and whose values are `File` instances.

The payload object is processed by a sequence of plugins. The resultant payload
object's `files` and `assets` properties represent corresponding files generated 
in the output directory.

## Payload

The payload is a `\stdObject` instance with these properties:

1. `root` - A `\SplFileInfo` object for the directory containing site files;
2. `files` - Associative array. Keys are pathnames (with leading `/`) beneath `root`, and values are `File` instances;
3. `assets` - Associative array. Keys are pathnames (with leading `/`) beneath `root`, and values are `File` instances;
4. `site` - Associative array of site metadata;

The first plugin always indexes files beneath `root`. Subsequent plugins manipulate those files.
There are two types of site files: _content_ files and _asset_ files.

_Content_ files are parsed for header properties and body content. Content files are 
available in the payload's `files` property. By default, files with these extensions 
are considered _content_ files:

* html
* md
* markdown
* txt
* xml
* rss

All other files are considered _asset_ files. They are not parsed, and they are copied 
verbatim into the output directory. Asset files are available in the payload's `assets` property.

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
provides a `Header\Ini` class to parse INI-formatted headers. The header must be parseable
into an associative array; this array's keys and values become available on the corresponding 
`File` instance via `\ArrayAccess` interface.

The body can also be whatever you want. Its content is available in the corresponding `File`
instance's `body` array key. A _content_ file's `body` is the content of the output file.
This repo provides a `Body\Parsedown` class to parse Markdown body content, converted with Parsedown.

The example content file above can be used like this:

```
echo $file['title'];
echo $file['date];
echo $file['body'];
```

## Plugins

A plugin is any class instance that implements the `PluginInterface` interface:

```
public function handle(object $payload);
```

### Collections

The collections plugin lets you pre-define groups of files
that meet specific criteria. The `Collections` constructor
accepts an associative array. Array keys are collection IDs,
and array values are the collection criteria.

```
$app->add(new Collections([
    'recent_news' => [
        'pattern' => '#^/news/.*#',
        'sortBy' => 'date',
        'reverse' => true,
        'limit' => 2
    ]
]));
```

Each pre-populated collection is available in the `$payload->site['collections']`
array. The `recent_news` collection above, for example, is available at 
`$payload->site['collections']['recent_news']`; its keys are the file pathnames, 
and its values are `File` instances.

Each collection's criteria accepts these keys:

* `pattern`: string. A regular expression to match file pathnames, passed verbatim to `preg_match()`;
* `sortBy`: string. A file property to sort by;
* `reverse`: boolean. Reverse the direction of sorted Files;
* `limit`: integer. Limit the number of Files;
* `where`: array. Filter files with criteria;

The `where` property is an associative array. Its keys are File property names,
and its values are either strings or numeric arrays.

If a string, Files are filtered such that the File property matching 
the array key equals the array string value.

```
[
    'where' => [
        'title' => 'My title'
    ]
]
```

If an array, Files are filtered such that the File property matching
the array key satisfies the comparison with the value's 
first element. The default comparison operator is `=`, but it may
be overridden with the value's optional second element. Here are 
several examples:

Find posts not authored by Josh:

```
[
    'where' => [
        'author' => ['Josh', '!=']
    ]
]
```

Possible operators are:

* `=` - Equals
* `!=` - Not equals
* `>` - Greater than
* `>=` - Greater than or equal to
* `<` - Less than
* `<=` - Less than or equal to
* `~` - Matches (value must be a valid PHP regular expression with delimiters)
* `!~` - Does not match (value must be a valid PHP regular expression with delimiters)

The Collections plugin also exposes a method to query
Files in subsequent plugins.

```
$payload->site['api']->query([...]);
```

The `query()` method accepts the same criteria as a pre-defined
collection and returns an associative array; its keys are
pathnames and its values are `File` instances.

### Drafts

Use the Drafts plugin to omit Files from output.
A content file is considered a draft if its header
contains a `draft` property with a truthy value.

### Publish Date

Use the Publish Date plugin to convert a header
property value into a `\DateTime` object
for use in subsequent plugins. It is assumed the
original header property value is a valid date/time
string that can be parsed by PHP.

### Twig

Use the Twig plugin to render content files using
Twig templates. The Twig plugin constructor
accepts two arguments: the path to the templates
directory, and an optional Twig environment configuration.

```
$app->add(new Twig(
    './path/to/templates/`,
    [
        'cache' => false
    ]
));
```

The default template is `page.twig`. You can choose a different
template for each content file by entering the Twig template
name in the file header's `template` property.

```
title = "My post"
template = "custom-post.twig"

---

<p>Post content goes here.</p>
```

Every Twig template has these variables:

* `page` - The current `File` being rendered;
* `current_url` - The current page URL;
* `site` - The `$payload->site` object;

The `page` object is a `File` instance, and therefore
it can be used as an array to access header
properties and the body content.

The `site` object contains site-wide metadata. Among
other data, it contains pre-defined collections (see above)
that may be accessed like this:

```
{% for pathname, file in site.collections["collection-name"] %}
    <a href="{{ pathname }}">{{ file.title }}</a>
{% endfor %}
```

You may also run queries at render-time using the Collection
plugin's `query()` method like this:

```
{% set files = site.api.query({
    where: { "author": ["Josh", "!="] },
    sortBy: "date",
    reverse: true,
    limit: 5
}) %}
```

If you do NOT want a content file to be rendered with Twig, 
add a header property to the file with name `twig` and value `false`:

```
title: "My post"
twig: false

---

<p>This post will not use Twig.</p>
```
