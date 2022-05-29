<?php
namespace Nmc\Ssg;

class HeaderIni implements PluginInterface
{
    public function handle(object $payload)
    {
        foreach ($payload->files as $pathname => $file) {
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
