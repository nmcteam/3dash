# 3dash Dates Plugin

The Dates plugin converts [content file](../files.md#content-files) 
header property values into PHP `\DateTime` instances for use in 
subsequent plugins.

For example, the `date` header property in this example content file 
can be used as a `\DateTime` value in subsequent plugins:

```
title = "My post"
author = "Josh"
date = "2022-06-06"

---

<p>This is my post.</p>
```

The header `date` property value MUST be a valid date/time string 
according to <https://www.php.net/manual/datetime.format.php>.

## Use the plugin

To use this plugin, add an instance of the `Date` class to your
3dash app. Its constructor accepts an array of header property
names that should be converted into `\DateTime` instances.

```
$app->add(new Ssg\Plugins\Dates(['published_at', 'updated_at']));
```

The constructor method accepts an optional second argument
to define a specific `\DateTimeZone` used by the new `\DateTime`
instances:

```
$app->add(new Ssg\Plugins\Dates(
    ['published_at', 'updated_at'],
    new \DateTimeZone('UTC')
));
```

If omitted, the default PHP timezone (as defined in `php.ini`)
is used instead.
