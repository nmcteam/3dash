<?php
namespace Codeguy\Ssg;

class FileCollection implements \IteratorAggregate
{
    protected $files;

    public function __construct(array $files)
    {
        $this->files = $files;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->files);
    }

    public function each(callable $callback)
    {
        $result = $this->files;
        foreach ($result as $pathname => $file) {
            $result[$pathname] = $callback($pathname, $file);
        }

        return new self($result);
    }

    public function recent(int $count = 5)
    {
        return new self(array_slice($this->sortByDate('DESC'), 0, $count));
    }

    public function match(string $pattern)
    {
        $result = array_filter($this->files, function ($v, $k) use ($pattern) {
            return preg_match($pattern, $k);
        }, \ARRAY_FILTER_USE_BOTH);

        return new self($result);
    }

    public function notMatch(string $pattern)
    {
        $result = array_filter($this->files, function ($v, $k) use ($pattern) {
            return preg_match($pattern, $k) !== 1;
        }, \ARRAY_FILTER_USE_BOTH);

        return new self($result);
    }

    public function sortByTitle(string $dir = 'ASC')
    {
        if ($dir !== 'ASC' && $dir !== 'DESC') {
            throw new \Exception('Invalid sort direction: ' . $dir);
        }

        $result = $this->files;
        uasort($result, function ($a, $b) use ($dir) {
            if ($dir === 'ASC') {
                return $a->getTitle() <=> $b->getTitle();
            } else {
                return $b->getTitle() <=> $a->getTitle();
            }
        });

        return new self($result);
    }

    public function sortByDate(string $dir = 'DESC')
    {
        if ($dir !== 'ASC' && $dir !== 'DESC') {
            throw new \Exception('Invalid sort direction: ' . $dir);
        }

        $result = $this->files;
        uasort($result, function ($a, $b) use ($dir) {
            if ($dir === 'ASC') {
                return $a->getDateTime() <=> $b->getDateTime();
            } else {
                return $b->getDateTime() <=> $a->getDateTime();
            }
        });

        return new self($result);
    }

    public function toArray(): array
    {
        return $this->files;
    }
}
