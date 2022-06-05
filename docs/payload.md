## 3dash Payload

A 3dash app has one _payload_; it's a `\stdObject` instance. A reference to the payload object 
is provided to each 3dash plugin, one at a time, in the same order the plugins were added.

The payload object has these properties:

1. `root` - `\SplFileInfo` instance for the directory containing site files.
2. `files` - Associative array of `File` instances. Keys are pathnames (with leading `/`) beneath `root`.
3. `assets` - Associative array of `File` instances. Keys are pathnames (with leading `/`) beneath `root`.
4. `site` - Associative array of site metadata and tools.

The `files` property is an associative array of _content_ files; its keys are pathnames of generated files, and its 
values are `File` instances. Plugins manipulate this payload object property to add, update, or remove content files.
The pathnames may be manipulated, too, to reflect the desired generated file paths and names.

The `assets` property is an associative array of _asset_ files; its keys are pathnames of generated files, and its 
values are `File` instances. Plugins manipulate this payload object property to add, update, or remove asset files.
The pathnames may be manipulated, too, to reflect the desired generated file paths and names.

The `site` property is an associative array for site-wide metadata. It is also a good place to keep information
and tools needed by subsequent plugins.
