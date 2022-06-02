<?php
// Copyright (c) 2022 Josh Lockhart, and New Media Campaigns

// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:

// The above copyright notice and this permission notice shall be included in all
// copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.

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
$app->add(new Ssg\Plugins\FilesystemWriter('./public'));
$app->run();
