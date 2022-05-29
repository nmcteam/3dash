<?php
namespace Nmc\Ssg;

class Twig implements PluginInterface
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function handle(object $payload)
    {
        // Ensure Twig exists
        if (class_exists('\Twig\Loader\FilesystemLoader') === false) {
            throw new \Exception('Twig is not loaded');
        }

        // Get file collections
        $twig_files = array_filter($payload->files, function ($pathname) {
            return preg_match("#\.twig$#", $pathname);
        }, \ARRAY_FILTER_USE_KEY);
        $tpl_files = array_filter($twig_files, function ($pathname) {
            return preg_match("#\.tpl\.twig$#", $pathname);
        }, \ARRAY_FILTER_USE_KEY);
        $content_files = array_filter($twig_files, function ($pathname) {
            return preg_match("#\.tpl\.twig$#", $pathname) !== 1;
        }, \ARRAY_FILTER_USE_KEY);

        // Init Twig and render files
        $loader = new \Twig\Loader\ArrayLoader(array_map(fn($file) => $file->getBody(), $twig_files));
        $twig = new \Twig\Environment($loader, $this->config);
        foreach ($content_files as $pathname => $file) {
            // Render file
            $file->setBody($twig->render($pathname, [
                'page' => $file,
                'site' => $payload->site
            ]));

            // Update payload pathname for file
            $new_pathname = preg_replace("#\.twig$#", '', $pathname);
            $payload->files[$new_pathname] = $file;
            unset($payload->files[$pathname]);
        };

        // Remove .tpl.twig files from payload
        foreach($tpl_files as $pathname => $file) {
            unset($payload->files[$pathname]);
        }
    }
}
