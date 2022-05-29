<?php
namespace Nmc\Ssg;

class PublishDate implements PluginInterface
{
    /**
     * @var string The date field name
     */
    protected $field;

    /**
     * @var string Time zone string
     */
    protected $tz;

    /**
     * Constructor
     * 
     * @param string $tz
     */
    public function __construct(string $field, string $tz = null)
    {
        $this->field = $field;
        $this->tz = $tz ?? date_default_timezone_get();
    }

    /**
     * Handle payload
     * 
     * @param object $payload
     */
    public function handle(object $payload)
    {
        foreach ($payload->files as $pathname => $file) {
            if (isset($file[$this->field])) {
                $date_string = $file[$this->field];
                $file[$this->field] = new \DateTime($file[$this->field], new \DateTimeZone($this->tz));
            }
        }
    }
}
