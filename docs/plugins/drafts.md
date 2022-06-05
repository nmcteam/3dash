# 3dash Drafts

The Drafts plugin prevents [content files](../files.md#content-files) 
from being generated. A content file is considered a draft if its header
contains a `draft` property with a truthy value.

This is an example draft file:

```
title = "My WIP post"
author = "Josh"
draft = true

---

<p>This post will not be generated while it is a draft.</p>
```

## Use the plugin

To use this plugin, add an instance of the `Drafts` class to your
3dash app like this:

```
$app->add(new Ssg\Plugins\Draft());
```
