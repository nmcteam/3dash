<?php
namespace Codeguy\Ssg\Plugins\Headers;

use Codeguy\Ssg\Payload;
use Codeguy\Ssg\File;
use Codeguy\Ssg\FileCollection;
use Codeguy\Ssg\Interfaces\PluginInterface;

class IniHeader implements PluginInterface
{
    public function handle(Payload $payload): void
    {
        foreach ($payload['files'] as $pathname => $file) {
            if ($file->getOriginalHeader() === '') {
                continue;
            }
            $header_data = parse_ini_string($file->getOriginalHeader());
            if ($header_data === false) {
                throw new \Exception('Failed to parse header as INI string for file: ' . $file->getPathname());
            }
            $file->setHeader($header_data);
        }
    }
}
