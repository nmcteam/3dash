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
     * @var array
     */
    protected $collections;

    /**
     * @var array Reference to app payload
     */
    protected $payload;

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
        // Store reference to payload
        $this->payload = $payload;

        // Init collections to empty arrays
        $this->collections = array_fill_keys(array_keys($this->definitions), []);

        // Populate collections
        foreach ($this->definitions as $c_name => $c_def) {
            $this->collections[$c_name] = $this->query($c_def);
        }

        // Enable arbitrary queries from subsequent plugins
        $payload->site['collections'] = $this;
    }

    /**
     * Get collection by name
     * 
     * @param string $name
     * @return array|null
     */
    public function get(string $name): ?array
    {
        return $this->collections[$name] ?? null;
    }

    /**
     * Query for dynamic collection
     * 
     * @param array $criteria
     * @return array
     */
    public function query(array $criteria): array
    {
        // Initial result set
        $hits = $this->payload->files;

        // Require pattern
        if ($criteria['pattern']) {
            $hits = array_filter($hits, function ($k) use ($criteria) {
                return preg_match($criteria['pattern'], $k);
            }, \ARRAY_FILTER_USE_KEY);
        }

        // Where?
        if ($hits && isset($criteria['where']) && is_array($criteria['where'])) {
            foreach ($where as $prop_name => $prop_criteria) {
                // Stop if there are no more hits to filter
                if (!$hits) {
                    break;
                }

                // Filter hits
                if (is_array($prop_criteria)) {
                    // Criteria can be an array like this: ["value", "operator"]
                    $prop_value = $prop_criteria[0];
                    $prop_comparison = $prop_criteria[1] ?? '=';
                } else {
                    // Or criteria can be a simple string, in which case we do a === comparison
                    $prop_value = $prop_criteria;
                    $prop_comparison = '=';
                }
                $hits = array_filter($hits, function ($item) use ($prop_name, $prop_value, $prop_comparison) {
                    $result = true;
                    switch ($prop_comparison) {
                        case '=':
                            $result = $item[$prop_name] === $prop_value;
                            break;
                        case '!=':
                            $result = $item[$prop_name] !== $prop_value;
                            break;
                        case '<':
                            $result = $item[$prop_name] < $prop_value;
                            break;
                        case '<=':
                            $result = $item[$prop_name] <= $prop_value;
                            break;
                        case '>':
                            $result = $item[$prop_name] > $prop_value;
                            break;
                        case '>=':
                            $result = $item[$prop_name] >= $prop_value;
                            break;
                        case '~':
                            $result = preg_match($prop_value, $item[$prop_name]);
                            break;
                        case '!~':
                            $result = preg_match($prop_value, $item[$prop_name]) !== 1;
                            break;
                        default:
                            $result = $item[$prop_name] === $prop_value;
                            break;
                    }

                    return $result;
                });
            }
        }

        // Sort?
        if ($hits && isset($criteria['sortBy'])) {
            uasort($hits, function ($file_a, $file_b) use ($criteria) {
                return $file_a[$criteria['sortBy']] <=> $file_b[$criteria['sortBy']];
            });
        }

        // Reverse?
        if ($hits && isset($criteria['reverse'])) {
            $hits = array_reverse($hits);
        }

        // Limit?
        if ($hits && isset($criteria['limit']) && (int)$criteria['limit'] > 0) {
            $hits = array_slice($hits, 0, $criteria['limit']);
        }

        return $hits;
    }
}
