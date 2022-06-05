# 3dash Static Site Generator

3dash is a tiny PHP static site generator created as a weekend project. We wanted 
a simple PHP solution, similar to Metalsmith, to create a tiny personal blog.

Here's the gist: there is a [payload object](./docs/payload.md) that manages your 
site [content](./docs/files.md#content-files) and [asset](./docs/files.md#asset-files)
files. The payload object's properties are associative arrays. The array keys are 
pathnames of generated site files (e.g. "/about/josh.html"), and the array values are 
[File](./docs/files.md#the-file-class) instances that determine the content of 
generated site files.

The payload object is processed by a sequence of [plugins](./docs/plugins.md). Plugins 
are run in the order they are added. Each plugin may manipulate the payload object. 
For example, a plugin may add, update, or remove site files. A plugin may also provide 
tools intended for subsequent plugins. The final plugin generates and outputs site files.

## Documentation

* [Get Started](./docs/get-started.md)
* [Payload](./docs/payload.md)
* [Files](./docs/files.md)
* [Plugins](./docs/plugins.md)
    * [Collections](./docs/plugins/collections.md)
    * [Dates](./docs/plugins/dates.md)
    * [Drafts](./docs/plugins/drafts.md)
    * [Filesystem Writer](./docs/plugins/filesystem-writer.md)
    * [Images](./docs/plugins/images.md)
    * [Twig](./docs/plugins/twig.md)

## Issues

Find a bug? Have suggestions? Open an issue here:

<https://github.com/nmcteam/3dash/issues>

## Contribute

Help us improve 3dash. Contribute on [GitHub](https://github.com/nmcteam/3dash)!

## License

3dash is released under the [MIT Public License](./LICENSE).

## Authors

3dash is created and maintained by [New Media Campaigns](https://www.newmediacampaigns.com).
