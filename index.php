<?php
require './vendor/autoload.php';

use Nmc\Ssg\App;
use Nmc\Ssg\Body\Parsedown as Body;
use Nmc\Ssg\Header\Ini as Header;
use Nmc\Ssg\Plugins\Collections;
use Nmc\Ssg\Plugins\Drafts;
use Nmc\Ssg\Plugins\FilesystemWriter;
use Nmc\Ssg\Plugins\PublishDate;
use Nmc\Ssg\Plugins\Twig;

$app = new App(__DIR__ . '/site/content');
$app->add(new Header());
$app->add(new Drafts());
$app->add(new PublishDate('date'));
$app->add(new Body());
$app->add(new Collections([
    'recent_news' => [
        'pattern' => '#^/news/.*#',
        'sortBy' => 'date',
        'reverse' => true,
        'limit' => 2
    ]
]));
$app->add(new Twig(__DIR__ . '/site/templates'));
$app->add(new FilesystemWriter('./build'));
$app->run();
