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

class Images implements PluginInterface
{
    protected $manager;
    protected $payload;
 
    public function __construct(string $driver = 'imagick')
    {
        if (class_exists('\Intervention\Image\ImageManager') === false) {
            throw new \Exception('Intervention\Image\ImageManager class not found');
        }
        $this->manager = new \Intervention\Image\ImageManager(['driver' => $driver]);
    }

    public function handle(object $payload)
    {
        // Store reference to payload
        $this->payload = $payload;

        // Expose image resizer API to subsequent plugins
        $payload->site['images'] = $this;
    }

    /**
     * Resize a File and return its pathname
     * 
     * @param File $file
     * @param array $transforms
     * @return string
     */
    public function resize(string $path, array $transforms): string
    {
        // Get asset
        $file = $this->payload->assets[$path] ?? null;
        if (!$file) {
            throw new \Exception('Asset not found: ' . $path);
        }

        // Verify file is image
        $extension = $file->getExtension();
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']) === false) {
            throw new \Exception('File is not an image: ' . $file['path']);
        }

        // TODO: Verify criteria

        // TODO: Apply criteria
        $image = $this->manager->make((string)$file->getBody());
        $image->fit(640, 480);

        // Add resized image file to payload assets
        $path = pathinfo($file['path'], \PATHINFO_DIRNAME);
        $basename = pathinfo($file['path'], \PATHINFO_FILENAME);
        $new_pathname = $path . '/' . $basename . '-resized' . '.' . $extension;
        $this->payload->assets[$new_pathname] = new File($image->stream(), [
            'path' => $new_pathname
        ]);

        return $new_pathname;
    }
}
