<?php
require './vendor/autoload.php';

use Nmc\Ssg\App;
use Nmc\Ssg\Drafts;
use Nmc\Ssg\FilesystemWriter;
use Nmc\Ssg\HeaderIni;
use Nmc\Ssg\Twig;

$app = new App('./site');
$app->add(new HeaderIni());
$app->add(new Drafts());
$app->add(new Twig());
$app->add(new FilesystemWriter('./build'));
$app->run();
