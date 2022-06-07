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

use GuzzleHttp\Psr7;
use Psr\Http\Message\StreamInterface;

class File implements \ArrayAccess
{
    /**
     * @var array File metadata
     */
    protected $props;

    /**
     * Constructor
     * 
     * @param StreamInterface $stream
     * @param array $props
     */
    public function __construct(StreamInterface $stream, array $props = [])
    {
        $this->props = $props;
        $this->props['body'] = $stream;
    }

    /**
     * Create File for string input
     * 
     * @param string $input Plain string content
     * @param array $props File metadata
     * @return File
     */
    public static function fromString(string $input, array $props = [])
    {
        return new self(Psr7\Utils::streamFor($input), $props);
    }

    /**
     * Create File for resource input
     * 
     * @param resource $input PHP resource handle
     * @param array $props File metadata
     * @return File
     */
    public static function fromResource(\resource $resource, array $props = [])
    {
        return new self(Psr7\Utils::streamFor($resource), $props);
    }

    /**
     * Create File for file pathname
     * 
     * @param string $input Pathname to readable file
     * @param array $props File metadata
     * @return File
     */
    public static function fromFile(string $pathname, array $props = [])
    {
        $handle = fopen($pathname, 'rb');
        if ($handle === false) {
            throw new \Exception('Could not open file: ' . $pathname);
        }
    
        return new self(Psr7\Utils::streamFor($handle), $props);
    }

    public function get(string $key)
    {
        return $this->props[$key] ?? null;
    }

    public function set(string $key, $value)
    {
        if ($key === 'body') {
            $value = Psr7\Utils::streamFor($value);
        }
        $this->props[$key] = $value;
    }

    public function add(array $props)
    {
        foreach ($props as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->props);
    }

    public function remove(string $key)
    {
        unset($this->props[$key]);
    }

    public function all(): array
    {
        return $this->props;
    }

    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }
}
