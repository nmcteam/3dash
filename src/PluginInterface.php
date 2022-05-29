<?php
namespace Nmc\Ssg;

interface PluginInterface
{
    public function handle(object $payload);
}
