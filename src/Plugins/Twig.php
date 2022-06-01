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

namespace Nmc\Ssg\Plugins;

use Nmc\Ssg\File;
use Nmc\Ssg\PluginInterface;

class Twig implements PluginInterface
{
    /**
     * @var \Twig\Loader\ArrayLoader
     */
    protected $loader;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * Constructor
     * 
     * @param array $config Used as second argument to \Twig\Environment::__construct()
     */
    public function __construct(string $templates_path, array $config = [])
    {
        if (class_exists('\Twig\Loader\FilesystemLoader') === false) {
            throw new \Exception('Twig is not loaded');
        }
        $this->loader = new \Twig\Loader\FilesystemLoader($templates_path);
        $this->twig = new \Twig\Environment($this->loader, $config);
    }

    /**
     * Get Twig environment
     * 
     * @return \Twig\Environment
     */
    public function getTwig(): \Twig\Environment
    {
        return $this->twig;
    }

    /**
     * Render Twig files found in app payload
     * 
     * @param object $payload
     * @return void
     */
    public function handle(object $payload)
    {
        foreach ($payload->files as $pathname => $file) {
            // Skip Twig if requested
            if (isset($file['twig']) && !$file['twig']) {
                continue;
            }
            
            // Get template
            $template = $file['template'] ?? 'page.twig';

            // Render file
            $file->setBody($this->twig->render($template, [
                'page' => $file,
                'current_url' => $pathname,
                'site' => $payload->site
            ]));
        };
    }
}
