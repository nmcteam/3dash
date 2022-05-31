<?php
namespace Nmc\Ssg\Plugins;

use Nmc\Ssg\File;
use Nmc\Ssg\PluginInterface;

class Twig implements PluginInterface
{
    /**
     * @var \Twig\Loader\ArrayLoader
     */
    protected $loader;

    /**
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     * Constructor
     * 
     * @param array $config Used as second argument to \Twig\Environment::__construct()
     */
    public function __construct(string $templates_path, array $config = [])
    {
        if (class_exists('\Twig\Loader\FilesystemLoader') === false) {
            throw new \Exception('Twig is not loaded');
        }
        $this->loader = new \Twig\Loader\FilesystemLoader($templates_path);
        $this->twig = new \Twig\Environment($this->loader, $config);
    }

    /**
     * Get Twig environment
     * 
     * @return \Twig\Environment
     */
    public function getTwig(): \Twig\Environment
    {
        return $this->twig;
    }

    /**
     * Render Twig files found in app payload
     * 
     * @param object $payload
     * @return void
     */
    public function handle(object $payload)
    {
        foreach ($payload->files as $pathname => $file) {
            if ($file instanceof File) {
                // Get template
                $template = $file['template'] ?? 'page.twig';

                // Render file
                $file['body'] = $this->twig->render($template, [
                    'page' => $file,
                    'site' => $payload->site
                ]);
            }
        };
    }
}
