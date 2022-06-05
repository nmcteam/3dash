# 3dash Collections Plugin

The collections plugin lets you pre-define groups of files
that meet specific criteria. The `Collections` class constructor
accepts an associative array. Array keys are collection IDs, and array 
values are collection criteria (see "Collection Criteria" below).

```
$app->add(new Collections([
    'recent_news' => [
        'pattern' => '#^/news/.*#',
        'sortBy' => 'date',
        'reverse' => true,
        'limit' => 2
    ]
]));
```

## Use collections

Each pre-populated collection is available with the `$payload->site['collections']->get()`
method. The `recent_news` collection above, for example, can be returned like this:

```
$files = $payload->site['collections']->get('recent_news')`;
```

The `get()` method returns an associative array; the array keys are pathnames, and
the array values are [File](../files.md#the-file-class) instances.

## Collection criteria

When you define a collection, you provide criteria. The criteria is an
associative array that may use the following keys:

* `pattern`: String. A regular expression matched against content file pathnames. This is passed verbatim to `preg_match()`.
* `sortBy`: String. A File instance property to sort by.
* `reverse`: Boolean. Reverse the direction of sorted File instances.
* `limit`: Integer. Limit the number of File instances in the collection.
* `where`: Array. Filter File instances with advanced criteria.

The `where` property is an associative array. Its keys are File property names,
and its values are either strings or one-dimensional arrays.

If a `where` criteria value is a string, File instances are filtered such that 
the File property matching the criteria key equals the criteria value.

```
[
    'where' => [
        'author' => 'Josh'
    ]
]
```

If a criteria value is a one-dimensional array, File instances are filtered such 
that the File property matching the criteria key satisfies the comparison with the 
criteria value's first element. The default comparison operator is `=`, but it may
be overridden with the criteria value's optional second element.

This example finds posts with titles that begin with "PHP":

```
[
    'where' => [
        'title' => ['/^PHP/', '~']
    ]
]
```

Possible comparison operators are:

* `=` - Equals
* `!=` - Not equals
* `>` - Greater than
* `>=` - Greater than or equal to
* `<` - Less than
* `<=` - Less than or equal to
* `~` - Matches (value passed verbatim to `preg_match()`)
* `!~` - Does not match (value passed verbatim to `preg_match()`)

## Adhoc queries

The Collections plugin provides a `query()` method to query File instances 
in subsequent plugins:

```
$files = $payload->site['collections']->query([...]);
```

The `query()` method accepts the same criteria as a pre-defined
collection, and it returns an associative array; the array keys are pathnames, 
and the array values are [File](../files.md#the-file-class) instances.
