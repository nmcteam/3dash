# 3dash Documentation

3dash is a tiny PHP static site generator created as a weekend project. We wanted a simple
PHP solution, similar to Metalsmith, to create a tiny personal blog.

Here's the gist: there is a [payload object](payload.md) that manages your site content and
asset files. The payload object's properties are associative arrays. The array keys are pathnames 
of the generated site files (e.g. "/about/josh.html"), and their values are [File](files.md)
instances that determine the content of the generated site files.

The payload object is processed by a sequence of plugins. Plugins are run in the order they 
are added. Each plugin may manipulate the payload object. For example, a plugin may add, update, 
or remove site files. A plugin may also provide tools intended for subsequent plugins.
After all plugins run, the resultant payload object's files are generated in the output directory.
