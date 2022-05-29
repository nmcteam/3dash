<?php
namespace Nmc\Ssg;

class Collections implements PluginInterface
{
    /**
     * Collection definitions
     * 
     * Associative array. Keys are the collection names, and each value is
     * an associative array with these keys:
     * 
     * `pattern` - Required. Regular expression for matching file pathnames.
     * `sortBy` - Optional. File header property.
     * `reverse` - Optional. Should sorted files be reversed?
     * `limit` - Optional. Should collection capacity be limited?
     * 
     * @var array
     */
    protected $definitions;

    /**
     * Constructor
     * 
     * @param array $definitions
     */
    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    public function handle(object $payload)
    {
        // Init collections to empty arrays
        $payload->site['collections'] = array_fill_keys(array_keys($this->definitions), []);

        // Populate collections
        foreach ($this->definitions as $c_name => $c_def) {
            // Require pattern
            if (empty($c_def['pattern'])) {
                throw new \Exception('Collection definition is missing a pattern: ' . $c_name);
            }

            // Collect matching files
            foreach ($payload->files as $pathname => $file) {
                if (preg_match($c_def['pattern'], $pathname)) {
                    $payload->site['collections'][$c_name][] = $file;
                }
            }

            // Stop if collection is empty
            if (empty($payload->site['collections'][$c_name])) {
                continue;
            }

            // Sort?
            if (isset($c_def['sortBy'])) {
                uasort($payload->site['collections'][$c_name], function ($file_a, $file_b) use ($c_def) {
                    return $file_a[$c_def['sortBy']] <=> $file_b[$c_def['sortBy']];
                });
            }

            // Reverse?
            if (isset($c_def['reverse'])) {
                $payload->site['collections'][$c_name] = array_reverse($payload->site['collections'][$c_name]);
            }

            // Limit?
            if (isset($c_def['limit']) && (int)$c_def['limit'] > 0) {
                $payload->site['collections'][$c_name] = array_slice($payload->site['collections'][$c_name], 0, $c_def['limit']);
            }
        }
    }
}
