<?php
namespace Nmc\Ssg;

class File extends \SplFileObject implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $props;

    public function __construct(string $pathname, array $props = [])
    {
        $this->props = $props;
        parent::__construct($pathname, 'rb');
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
