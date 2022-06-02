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
    /**
     * @var \Intervention\Image\ImageManager
     */
    protected $manager;

    /**
     * @var \stdObject
     */
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
     * Accepts the following transforms:
     * 
     * `width` - Required. Resized image width in pixels.
     * `height` - Optional. Resized image height in pixels.
     * `format` - Optional. Resized image format.
     * 
     * @param File $file
     * @param array $transforms
     * @return string
     */
    public function resize(string $path, array $transforms): string
    {
        // Get asset, Return original path if asset not found
        $file = $this->payload->assets[$path] ?? null;
        if (!$file) {
            return $path;
        }

        // Verify asset is image
        $extension = $file->getExtension();
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp']) === false) {
            throw new \Exception('File is not an image: ' . $file['path']);
        }

        // Output format
        $format = $transforms['format'] ?? $extension;
        if ($format === 'jpeg') {
            $format = 'jpg';
        }
        if (!in_array($format, ['jpg', 'png', 'webp', 'gif', 'tif'])) {
            throw new \Exception('Invalid output format for file: '. $path);
        }
    
        // Determine resized image path
        ksort($transforms);
        $hash = md5(json_encode($transforms));
        $path = pathinfo($file['path'], \PATHINFO_DIRNAME);
        $basename = pathinfo($file['path'], \PATHINFO_FILENAME);
        $new_pathname = sprintf(
            '%s/%s-%s.%s',
            $path,
            $basename,
            $hash,
            $format
        );

        // If resized asset already exists, return its path
        if (isset($this->payload->assets[$new_pathname])) {
            return $new_pathname;
        }

        // Verify criteria
        if (!isset($transforms['width'])) {
            throw new \Exception('Missing width for resized image: ' . $path);
        }

        // Resize
        $fit = $transforms['fit'] ?? 'cover';
        $image = $this->manager->make((string)$file->getBody());
        if ($transforms['width'] && $transforms['height']) {
            if ($fit === 'cover') {
                $image->fit($transforms['width'], $transforms['height']);
            } else {
                $image->resize($transforms['width'], $transforms['height'], fn($c) => $c->aspectRatio());
            }
        } else if ($transforms['width']) {
            $image->resize($transforms['width'], null, fn($c) => $c->aspectRatio());
        } else if ($transforms['height']) {
            $image->resize(null, $transforms['height'], fn($c) => $c->aspectRatio());
        }

        // Grayscale
        $grayscale = $transforms['grayscale'] ?? $transforms['greyscale'] ?? false;
        if ($grayscale) {
            $image->greyscale();
        }

        // Sharpen
        $image->sharpen($transforms['sharpen'] ?? 10);

        // Optimize jpg
        if ($format === 'jpg') {
            // Interlace
            $image->interlace();

            // Set chroma subsampling 4:2:0
            $image->getCore()->setSamplingFactors(['2x2', '1x1', '1x1']);
        }

        // Quality (jpg only)
        $quality = (int)($transforms['quality'] ?? 85);
        if ($quality < 0 || $quality > 100) {
            throw new \Exception('Invalid quality for file: ' . $path);
        }

        // Add resized image file to payload assets
        $this->payload->assets[$new_pathname] = File::fromString((string)$image->encode($format, $quality), [
            'path' => $new_pathname
        ]);

        return $new_pathname;
    }
}
