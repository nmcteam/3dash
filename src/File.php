<?php
namespace Codeguy\Ssg;

class File extends \SplFileInfo implements \ArrayAccess
{
    protected $original_header;
    protected $original_body;

    protected $header;
    protected $body;

    public function __construct(string $pathname, string $header, string $body)
    {
        parent::__construct($pathname);
        $this->original_header = $header;
        $this->original_body = $body;
        $this->header = [];
        $this->body = $body;
    }

    public function getOriginalHeader(): string
    {
        return $this->original_header;
    }

    public function getOriginalBody(): string
    {
        return $this->original_body;
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function setHeader(array $data): void
    {
        $this->header = $data;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->header[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->header[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->header[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->header[$offset]);
    }

    public function getTitle(): string
    {
        return $this->header['title'] ?? $this->getBasename();
    }

    public function getDateTime(string $tz = null): \DateTime
    {
        $date_string = $this->header['date'] ?? filectime($this->getPathname());
        if (!$tz) {
            $tz = date_default_timezone_get();
        }

        return new \DateTime($date_string, new \DateTimeZone($tz));
    }
}
