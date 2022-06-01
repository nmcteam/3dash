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
     * @var StreamInterface
     */
    protected $stream;

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

    public static function fromString(string $input, array $props = [])
    {
        return new self(Psr7\Utils::streamFor($input), $props);
    }

    public static function fromResource(resource $resource, array $props = [])
    {
        return new self(Psr7\Utils::streamFor($resource), $props);
    }

    public static function fromPath(string $pathname, array $props = [])
    {
        $handle = fopen($pathname, 'rb');
        if ($handle === false) {
            throw new \Exception('Could not open file: ' . $pathname);
        }
    
        return new self(Psr7\Utils::streamFor($handle), $props);
    }

    /**
     * Get props
     * 
     * @return array
     */
    public function getProps(): array
    {
        $new_props = $this->props;
        unset($new_props['body']);

        return $new_props;
    }

    /**
     * Get file extension
     * 
     * @return string|null
     */
    public function getExtension(): ?string
    {
        return pathinfo($this['path'] ?? '', \PATHINFO_EXTENSION);
    }

    /**
     * Set body as Psr7 stream
     * 
     * @param mixed $body
     * @throws \InvalidArgumentException if the $body arg is not valid.
     */
    public function setBody($body)
    {
        $this->props['body'] = Psr7\Utils::streamFor($body);
    }

    /**
     * Get body as Psr7 stream
     * 
     * @return StreamInterface|null
     */
    public function getBody(): ?StreamInterface
    {
        return $this['body'];
    }

    public function offsetExists($offset): bool
    {
        return isset($this->props[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->props[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->props[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->props[$offset]);
    }
}
