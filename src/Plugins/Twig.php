<?php
namespace Codeguy\Ssg\Plugins;

use Codeguy\Ssg\Payload;
use Codeguy\Ssg\FileCollection;
use Codeguy\Ssg\Interfaces\PluginInterface;

class Twig implements PluginInterface
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function handle(Payload $payload): void
    {
        // Ensure Twig exists
        if (class_exists('\Twig\Loader\FilesystemLoader') === false) {
            throw new \Exception('Twig is not loaded');
        }

        // Init Twig array loader
        $site = $payload['site'];
        $all_files = new FileCollection($payload['files']);
        $twig_files = $all_files->match("#\.twig$#");
        $tpl_files = $twig_files->match("#\.tpl\.twig$#");
        $content_files = $twig_files->notMatch("#\.tpl\.twig$#");
        $loader = new \Twig\Loader\ArrayLoader();
        foreach ($twig_files as $pathname => $file) {
            $loader->setTemplate($pathname, $file->getBody());
        }
        $twig = new \Twig\Environment($loader, $this->config);
        foreach ($content_files as $pathname => $file) {
            // Render body with Twig
            $body = $twig->render($pathname, [
                'site' => $site + ['pages' => $content_files],
                'page' => $file
            ]);
            $file->setBody($body);

            // Update file in payload
            $new_pathname = preg_replace("#\.twig$#", '', $pathname);
            $payload['files'][$new_pathname] = $file;
            unset($payload['files'][$pathname]);
        };

        // Remove .tpl.twig files from payload
        foreach($tpl_files as $pathname => $file) {
            unset($payload['files'][$pathname]);
        }
    }
}
