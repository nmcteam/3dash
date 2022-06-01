<?php
// Copyright (c) 2022 Josh Lockhart, and New Media Campaigns

// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:

// The above copyright notice and this permission notice shall be included in all
// copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.

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
     * @param string $root_path The absolute pathname to the site content directory
     * @param PluginInterface $indexer Optional. Inject custom file indexer.
     */
    public function __construct(string $root_path = __DIR__, PluginInterface $indexer = null) {
        $this->payload = (object)[
            'root' => new \SplFileInfo($root_path),
            'files' => [],
            'assets' => [],
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
