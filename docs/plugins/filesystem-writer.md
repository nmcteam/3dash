# 3dash Filesystem Writer Plugin

Every 3dash app needs a final plugin to generate and output the 
[content](../files.md#content-files) and [asset](../files.md#asset-files) 
files. This plugin generates site files in a local filesystem directory.

To use this plugin, add an instance of the `FilesystemWriter`
class to your 3dash app:

```
$app->add(new Ssg\Plugins\FilesystemWriter('./build'));
```

The constructor requires one string argument: the path to
a local filesystem directory.
