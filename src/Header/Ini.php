<?php
namespace Nmc\Ssg\Header;

use Nmc\Ssg\PluginInterface;

class Ini implements PluginInterface
{
    public function handle(object $payload)
    {
        foreach ($payload->files as $pathname => $file) {
            $header = $file['header'] ?? false;
            if (is_string($header)) {
                $data = parse_ini_string($file['header']);
                if ($data === false) {
                    throw new \Exception('Failed to parse header as INI string for file: ' . $pathname);
                }
                foreach ($data as $k => $v) {
                    $file[$k] = $v;
                }
            }
        }
    }
}
