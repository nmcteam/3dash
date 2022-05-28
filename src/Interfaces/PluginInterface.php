<?php
namespace Codeguy\Ssg\Interfaces;

use Codeguy\Ssg\Payload;

interface PluginInterface
{
    public function handle(Payload $payload): void;
}
