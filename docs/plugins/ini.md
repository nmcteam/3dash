# 3dash INI Plugin

The INI plugin converts [content file](../files.md#content-files) 
headers into associative arrays. This applies to all content files.

Content file header properties will not be accessible to app plugins 
until this plugin (or a similar header-parsing plugin) runs. Therefore,
it is highly recommended to run this (or a similar) plugin as soon as possible.

## Use the plugin

To use this plugin, add an instance of the `Ini` class to your
3dash app like this:

```
$app->add(new Ssg\Header\Ini());
```
