# 3dash Twig Templates

The Twig plugin renders [content files](../files.md#content-files) using 
Twig templates. The Twig  plugin constructor accepts two arguments: the path 
to the templates directory, and an optional Twig environment configuration.

```
$app->add(new Twig(
    './path/to/templates/`,
    [
        'cache' => false
    ]
));
```

## Content templates

The default template is `page.twig`. You may choose a different template 
for each content file by specifying the Twig template name in the file 
header's `template` property.

```
title = "My post"
template = "custom-post.twig"

---

<p>Post content goes here.</p>
```

## Template variables

Every Twig template has these variables:

* `page` - The current [File](../files.md#the-file-class) instance.
* `current_url` - The current page pathname with leading `/`.
* `site` - The [payload](../payload.md) object's `site` property.

The `page` Twig variable is a [File](../files.md) instance, and therefore may be 
used as an array to access its [header properties and body content](../files.md#content-files).

The `site` Twig variable contains site-wide metadata and other tools provided by 
previous plugins. For example, if you defined custom [collections](./collections.md) 
before adding this Twig plugin, you can access those collections in your Twig 
templates like this:

```
{% for pathname, file in site.collections.get("collection-name") %}
    <a href="{{ pathname }}">{{ file.title }}</a>
{% endfor %}
```

You may also run custom queries at render-time using the [collection](./collections.md) 
plugin's `query()` method like this:

```
{% set files = site.collections.query({
    where: { "author": ["Josh", "!="] },
    sortBy: "date",
    reverse: true,
    limit: 5
}) %}
```

## Skip Twig

If you do NOT want a content file to be rendered with Twig, add a header property 
to the appropriate content file with name `twig` and value `false`:

```
title: "My post"
twig: false

---

<p>This post will not use Twig.</p>
```
