<?php
namespace Nmc\Ssg\Body;

use Nmc\Ssg\File;
use Nmc\Ssg\PluginInterface;

class Parsedown implements PluginInterface
{
    public function handle(object $payload)
    {
        if (class_exists('\Parsedown') === false) {
            throw new \Exception('Parsedown not loaded');
        }
        $parsedown = new \Parsedown();
        foreach ($payload->files as $pathname => $file) {
            if ($file instanceof File && preg_match("#\.(md|markdown)$#", $pathname)) {
                $file['body'] = $parsedown->text($file['body']);
                $new_pathname = preg_replace("#\.(md|markdown)$#", ".html", $pathname);
                $payload->files[$new_pathname] = $file;
                unset($payload->files[$pathname]);
            }
        }
    }
}
