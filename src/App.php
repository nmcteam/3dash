<?php
namespace Nmc\Ssg;

class App
{
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
     * @param string $content_path The absolute pathname to the content directory
     * @param PluginInterface $indexer Optional. Inject custom file indexer.
     */
    public function __construct(string $content_path = __DIR__, PluginInterface $indexer = null) {
        $this->payload = (object)[
            'files_path' => new \SplFileInfo($content_path),
            'files' => [],
            'site' => []
        ];
        $this->plugins = [$indexer ?? new Indexer()];
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
