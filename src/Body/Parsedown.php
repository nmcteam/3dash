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

namespace Nmc\Ssg\Body;

use Nmc\Ssg\File;
use Nmc\Ssg\PluginInterface;

class Parsedown implements PluginInterface
{
    public function handle(object $payload)
    {
        if (class_exists('\Parsedown') === false) {
            throw new \Exception('Parsedown not loaded');
        }
        $parsedown = new \Parsedown();
        foreach ($payload->files as $pathname => $file) {
            if (preg_match("#\.(md|markdown)$#", $pathname)) {
                $file->setBody($parsedown->text((string)$file->getBody()));
                $new_pathname = preg_replace("#\.(md|markdown)$#", ".html", $pathname);
                $payload->files[$new_pathname] = $file;
                unset($payload->files[$pathname]);
            }
        }
    }
}
