<?php
namespace Nmc\Ssg;

class Drafts implements PluginInterface
{
    public function handle(object $payload)
    {
        foreach ($payload->files as $pathname => $file) {
            if ($file['draft']) {
                unset($payload->files[$pathname]);
            }
        }
    }
}
