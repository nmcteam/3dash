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
use Psr\Http\Message\StreamInterface;

class FilesystemWriter implements PluginInterface
{
    protected $file_mode;
    protected $dir_mode;
    protected $owner;
    protected $group;

    public function __construct(
        string $output_dir,
        int $file_mode = 0664,
        int $dir_mode = 0775,
        string $owner = null,
        string $group = null
    ) {
        $this->file_mode = $file_mode;
        $this->dir_mode = $dir_mode;
        $this->owner = $owner;
        $this->group = $group;
        $this->output_dir = new \SplFileInfo($output_dir);
        if ($this->output_dir->isDir() === false || $this->output_dir->isWritable() === false) {
            throw new \Exception('Output directory is not writable: ' . $output_dir);
        }
    }

    public function handle(object $payload)
    {
        // @TODO: Clean build dir
    
        // Combine files
        // @TODO: Error if file name conflict? Would happen only if a 
        // file was defined manually after initial indexing.
        $all_files = array_merge($payload->files, $payload->assets);

        // Write files
        foreach ($all_files as $pathname => $file) {
            // Skip unless File has body
            if ($file['body'] instanceof StreamInterface === false) {
                continue;
            }

            // Create output directory
            $output_file = new \SplFileInfo($this->output_dir->getRealPath() . '/' . ltrim($pathname, '/'));
            $output_path = $output_file->getPath();
            $output_pathname = $output_file->getPathname();
            if (!is_dir($output_path) && mkdir($output_path, $this->dir_mode, true) === false) {
                throw new \Exception('Failed to create output directory for file: ' . $output_pathname);
            }
            if (!is_writable($output_path)) {
                throw new \Exception('Output directory is not writable for file: ' . $output_pathname);
            }

            // Create output file
            $output_handle = fopen($output_pathname, 'wb');
            if ($output_handle === false) {
                throw new \Exception('Failed to open output file: ' . $output_pathname);
            }
            $file['body']->rewind();
            while ($file['body']->eof() === false) {
                fwrite($output_handle, $file['body']->read(4096));
            }
            fclose($output_handle);

            // Set permissions
            if (chmod($output_pathname, $this->file_mode) === false) {
                throw new \Exception('Failed to change file mode for: ' . $output_pathname);
            }
            if ($this->owner && chown($output_pathname, $this->owner) === false) {
                throw new \Exception('Failed to change owner for file: ' . $output_pathname);
            }
            if ($this->group && chgrp($output_pathname, $this->group) === false) {
                throw new \Exception('Failed to change group for file: ' . $output_pathname);
            }
        }
    }
}
