# 3dash Plugins

A 3dash app is effectively a sequence of plugins that may manipulate 
the app's payload object. Plugins are invoked in the order they are added.

After all plugins finish, the files represented by the app payload are 
generated in the app output directory.

A plugin is any object that implements the `PluginInterface` interface:

```
public function handle(object $payload);
```

## Directory

* [Collections](./plugins/collections.md)
* [Images](./plugins/images.md)
* [Twig](./plugins/twig.md)
* [Drafts](./plugins/drafts.md)
* [Dates](./plugins/dates.md)
