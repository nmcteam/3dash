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

Now you can add plugins. The order in which you add plugins is important!
Plugins are run in the order they are added.

Add plugins that parse site files first, then add plugins that act 
on the parsed file data. Also, add plugins that provide tools _before_ 
plugins that need those tools.

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
```

The final plugin is responsible for generating and outputting files to 
the appropriate location. The 3dash `FilesystemWriter` plugin generates files 
in a local filesystem output directory (good for local or CI/CD workflows).
However, you may use a custom plugin instead. For example, a custom plugin may 
generate and upload files to AWS, GitHub Pages, or CloudFlare Pages.

```
$app->add(new Ssg\Plugins\FilesystemWriter('./public'));
```

And finally, you must invoke `$app->run()` to kick things off.

```
$app->run();
```
