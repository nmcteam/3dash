<?php
namespace Codeguy\Ssg;

use Codeguy\Ssg\Interfaces\HeaderParserInterface;
use Codeguy\Ssg\Interfaces\PluginInterface;
use Codeguy\Ssg\Plugins\IndexFiles;
use Codeguy\Ssg\Plugins\ParseFiles;

class App
{
    protected $root;
    protected $payload;
    protected $plugins;

    public function __construct(
        string $root
    ) {
        // Validate root dir
        $root = new \SplFileInfo($root);
        if ($root->isDir() === false || $root->isReadable() === false) {
            throw new \Exception('Root directory is not readable');
        }

        // Init payload
        $this->payload = new Payload([
            'site' => [
                'root' => $root
            ],
            'files' => []
        ]);

        // Init plugins
        $this->plugins = [
            new IndexFiles()
        ];
    }
 
    public function add(PluginInterface $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    public function run(): Payload
    {  
        foreach ($this->plugins as $plugin) {
            $plugin->handle($this->payload);
        }

        return $this->payload;
    }
}
