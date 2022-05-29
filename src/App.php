<?php
namespace Nmc\Ssg;

class App
{
    /**
     * @var \SplFileInfo App root directory. Contains the `files/` and `assets/` directories.
     */
    protected $root;

    /**
     * @var object The payload passed into each plugin in sequence
     */
    protected $payload;

    /**
     * @var array Numeric-array of PluginInterface instances
     */
    protected $plugins;

    /**
     * Constructor
     * 
     * @param string $root The absolute pathname to the application root directory
     */
    public function __construct(string $root = __DIR__) {
        $this->root = new \SplFileInfo($root);
        $this->payload = (object)[
            'files_path' => new \SplFileInfo($this->root->getRealPath() . '/files'),
            'assets_path' => new \SplFileInfo($this->root->getRealPath() . '/assets'),
            'files' => [],
            'assets' => [],
            'site' => []
        ];
        $this->plugins = [new IndexFiles()];
    }
 
    /**
     * Add plugin
     * 
     * @param PluginInterface $plugin
     * @return void
     */
    public function add(PluginInterface $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    /**
     * Run application
     * 
     * @return object The resultant payload after being processed by all plugins
     */
    public function run(): object
    {  
        foreach ($this->plugins as $plugin) {
            $plugin->handle($this->payload);
        }

        return $this->payload;
    }
}
