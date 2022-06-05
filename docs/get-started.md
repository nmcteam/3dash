# Get Started

## Install with Composer

Install 3dash with Composer:

```
composer require nmcteam/3dash
```

If you prefer to use the Images, Twig, or Parsedown plugins, you'll
want to install these third-party Composer packages, too:

```
composer require \
    intervention/image \
    twig/twig \
    erusev/parsedown;
```

## Start Your App

Create your app bootstrap file like this:

```
<?php
require './vendor/autoload.php';

use Nmc\Ssg;

$app = new Ssg\App(__DIR__ . '/site/content');
```

The `Ssg\App()` constructor accepts the path to your site content files.

Now you can add plugins; the order in which you add plugins is important!
Plugins are run in the order they are added.

Typically, you'll add plugins to parse site files first, then add plugins
that act on the parse file data.

The final plugin is responsible for outputting files to the desired 
location. The 3dash `FilesystemWriter` plugin generates files into
a local filesystem output directory. However, you may choose to use
a custom plugin to upload generated files to an S3 bucket, for example.

```
$app->add(new Ssg\Header\Ini());
$app->add(new Ssg\Body\Parsedown());
$app->add(new Ssg\Plugins\Drafts());
$app->add(new Ssg\Plugins\PublishDate('date'));
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
$app->add(new Ssg\Plugins\FilesystemWriter('./public'));
$app->run();
```

And finally, you must invoke `$app->run()` to kick things off.
