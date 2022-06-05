# 3dash Plugins

A 3dash app is effectively a sequence of plugins that may manipulate 
the app's payload object. The order in which you add plugins is important!
Plugins are run in the order they are added.

Add plugins that parse site files first, then add plugins that act 
on the parsed file data. Also, add plugins that provide tools _before_ 
plugins that need those tools.

## Interface

A plugin is any object that implements the `PluginInterface` interface:

```
public function handle(object $payload);
```

## Directory

* [Ini](./plugins/ini.md)
* [Parsedown](./plugins/parsedown.md)
* [Collections](./plugins/collections.md)
* [Images](./plugins/images.md)
* [Twig](./plugins/twig.md)
* [Drafts](./plugins/drafts.md)
* [Dates](./plugins/dates.md)
* [FilesystemWriter](./plugins/filesystem-writer.md)
