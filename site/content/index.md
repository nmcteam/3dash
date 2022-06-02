title = "Home"

---

3dash is a tiny static site generator created as a weekend project. I wanted a simple
PHP solution, similar to [Metalsmith](https://metalsmith.io/), to create a tiny personal blog.

The concept is simple. There is a _payload_ object with several properties, including
`files` and `assets`. These two properties are associative arrays whose keys are pathnames 
and whose values are `File` instances.

The payload object is processed by a sequence of plugins. The resultant payload
object's `files` and `assets` properties represent corresponding files generated 
in the output directory.

## Install

```
composer require nmcteam/3dash:0.1.0
```

## Example

```
<?php
require './vendor/autoload.php';

use Nmc\Ssg;

$app = new Ssg\App(__DIR__ . '/site/content');
$app->add(new Ssg\Header\Ini());
$app->add(new Ssg\Plugins\Drafts());
$app->add(new Ssg\Plugins\PublishDate('date'));
$app->add(new Ssg\Body\Parsedown());
$app->add(new Ssg\Plugins\Collections([
    'recent_news' => [
        'pattern' => '#^/news/.*#',
        'sortBy' => 'date',
        'reverse' => true,
        'limit' => 2
    ]
]));
$app->add(new Ssg\Plugins\Images());
$app->add(new Ssg\Plugins\Twig(__DIR__ . '/site/templates'));
$app->add(new Ssg\Plugins\FilesystemWriter('./build'));
$app->run();
```

## Contribute

Help us improve 3dash, and contribute on GitHub!

<https://github.com/nmcteam/3dash>

