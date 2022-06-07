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
composer require nmcteam/3dash
```

You'll likely want these suggested packages, too, to get 
the most out of 3dash:

```
composer require erusev/parsedown intervention/image twig/twig
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

## Documentation

See the README file for initial documentation. This will be expanded
and improved in the future.

[View README](https://github.com/nmcteam/3dash)

## Contribute

Help us improve 3dash, and contribute on GitHub!

<https://github.com/nmcteam/3dash>

## FAQs

### 1. Is 3dash stable and tested?

3dash should be considered experimental and certainly a pre-1.0 release.
We tossed it together as a weekend project to solve our immediate needs.
It works, but it's not officially tested. There are likely a few bugs.

### 2. Why not use something like Sculpin?

Indeed. Sculpin is awesome. But we wanted to scratch a personal itch
and build something much smaller and simpler. 3dash is not intended
for large complex sites. Think 1-5 page personal blog or brochure sites.

### 3. Do you provide support?

Personal one-on-one support? Nope. Email and phone? Nope. 3dash is
provided AS-IS under the MIT Public License. We'll try our best
to respond to issues in our GitHub repo, but that's the
best we can promise.

### 4. Does 3dash work with PHP 8?

Maybe. We haven't used PHP 8 yet. It should, though!
Definitely works with PHP 7.4.

### 5. I made a cool 3dash plugin. Will you add it to the main repo?

Maybe. Send us a link so we can check out your work! Worst case
we'll happily link to your external repo.
