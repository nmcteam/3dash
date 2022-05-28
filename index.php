<?php
require './vendor/autoload.php';

use Codeguy\Ssg\Plugins\Headers\IniHeader;
use Codeguy\Ssg\Plugins\Twig;
use Codeguy\Ssg\Plugins\Writers\FilesystemWriter;

$app = new \Codeguy\Ssg\App('./site');
$app->add(new IniHeader());
$app->add(new Twig([
    'cache' => false
]));
$app->add(new FilesystemWriter('./build'));
$result = $app->run();
