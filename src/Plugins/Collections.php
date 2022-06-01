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

use Nmc\Ssg\PluginInterface;

class Collections implements PluginInterface
{
    /**
     * Collection definitions
     * 
     * Associative array. Keys are the collection names, and each value is
     * an associative array with these keys:
     * 
     * `pattern` - Required. Regular expression for matching file pathnames.
     * `sortBy` - Optional. File header property.
     * `reverse` - Optional. Should sorted files be reversed?
     * `limit` - Optional. Should collection capacity be limited?
     * 
     * @var array
     */
    protected $definitions;

    /**
     * Constructor
     * 
     * @param array $definitions
     */
    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    public function handle(object $payload)
    {
        // Init collections to empty arrays
        $payload->site['collections'] = array_fill_keys(array_keys($this->definitions), []);

        // Populate collections
        foreach ($this->definitions as $c_name => $c_def) {
            // Require pattern
            if (empty($c_def['pattern'])) {
                throw new \Exception('Collection definition is missing a pattern: ' . $c_name);
            }

            // Collect matching files
            foreach ($payload->files as $pathname => $file) {
                if (preg_match($c_def['pattern'], $pathname)) {
                    $payload->site['collections'][$c_name][] = $file;
                }
            }

            // Stop if collection is empty
            if (empty($payload->site['collections'][$c_name])) {
                continue;
            }

            // Sort?
            if (isset($c_def['sortBy'])) {
                uasort($payload->site['collections'][$c_name], function ($file_a, $file_b) use ($c_def) {
                    return $file_a[$c_def['sortBy']] <=> $file_b[$c_def['sortBy']];
                });
            }

            // Reverse?
            if (isset($c_def['reverse'])) {
                $payload->site['collections'][$c_name] = array_reverse($payload->site['collections'][$c_name]);
            }

            // Limit?
            if (isset($c_def['limit']) && (int)$c_def['limit'] > 0) {
                $payload->site['collections'][$c_name] = array_slice($payload->site['collections'][$c_name], 0, $c_def['limit']);
            }
        }
    }
}
