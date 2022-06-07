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

namespace Nmc\Ssg;

class Indexer implements PluginInterface
{
    protected $content_formats;

    public function __construct(array $content_formats = [])
    {
        $this->content_formats = array_merge(
            [
                'html',
                'md',
                'markdown',
                'txt',
                'xml',
                'rss'
            ],
            $content_formats
        );
    }

    public function handle(object $payload)
    {
        $root_path = $payload->root->getRealPath();
        $directory = new \RecursiveDirectoryIterator($root_path, \FilesystemIterator::FOLLOW_SYMLINKS);
        $filter = new \RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) {
            // Skip hidden files and directories.
            if ($current->getFilename()[0] === '.') {
                return false;
            }

            // Otherwise recurse all files and dirs
            return true;
        });
        $iterator = new \RecursiveIteratorIterator($filter);

        // Iterate content directory and load files into app payload
        foreach ($iterator as $info) {
            // Only parse files
            if ($info->isFile() === false || $info->isReadable() === false) {
                continue;
            }

            // Validate file path
            $file_path = $info->getRealPath();
            if (stripos($file_path, $root_path) !== 0) {
                throw new \Exception('Found invalid file path: ' . $file_path);
            }
            $file_path_under_root = substr_replace($file_path, '', 0, strlen($root_path));

            // Read file content
            $file_pathname = $info->getPathname();
            $file_ext = trim(strtolower($info->getExtension()));
            if (in_array($file_ext, $this->content_formats)) {
                $file_content = file_get_contents($file_pathname);
                if ($file_content === false) {
                    throw new \Exception('Failed to read file: ' . $file_pathname);
                }

                // Split file content into header and body
                $file_parts = preg_split("#(\r?\n){1,}---(\r?\n){1,}#", $file_content, 2);
                if (empty($file_parts)) {
                    throw new \Exception('Found invalid file format: ' . $file_pathname);
                }

                // Add file to payload
                $header = '';
                $body = '';
                if (count($file_parts) === 1) {
                    $body = $file_parts[0];
                } else {
                    $header = $file_parts[0];
                    $body = $file_parts[1];
                }
                $payload->files[$file_path_under_root] = File::fromString($body, [
                    'header' => $header
                ]);
            } else {
                $payload->assets[$file_path_under_root] = File::fromFile($file_pathname);
            }
        }
    }
}
