<?php
namespace Nmc\Ssg\Plugins;

use Nmc\Ssg\File;
use Nmc\Ssg\PluginInterface;

class FilesystemWriter implements PluginInterface
{
    protected $file_mode;
    protected $dir_mode;
    protected $owner;
    protected $group;

    public function __construct(
        string $output_dir,
        int $file_mode = 0664,
        int $dir_mode = 0775,
        string $owner = null,
        string $group = null
    ) {
        $this->file_mode = $file_mode;
        $this->dir_mode = $dir_mode;
        $this->owner = $owner;
        $this->group = $group;
        $this->output_dir = new \SplFileInfo($output_dir);
        if ($this->output_dir->isDir() === false || $this->output_dir->isWritable() === false) {
            throw new \Exception('Output directory is not writable: ' . $output_dir);
        }
    }

    public function handle(object $payload)
    {
        // @TODO: Clean build dir
    
        // Write new files
        $root_path = $payload->files_path->getRealPath();
        foreach ($payload->files as $pathname => $file) {
            // Validate file path
            $file_path = $file->getRealPath();
            if (stripos($file_path, $root_path) !== 0) {
                throw new \Exception('Found invalid file path for file: ' . $file->getPathname());
            }

            // Create output directory
            $output_file = new \SplFileInfo($this->output_dir->getRealPath() . '/' . ltrim($pathname, '/'));
            $output_path = $output_file->getPath();
            $output_pathname = $output_file->getPathname();
            if (!is_dir($output_path) && mkdir($output_path, $this->dir_mode, true) === false) {
                throw new \Exception('Failed to create output directory for file: ' . $output_pathname);
            }
            if (!is_writable($output_path)) {
                throw new \Exception('Output directory is not writable for file: ' . $output_pathname);
            }

            // Create output file
            if ($file instanceof File) {
                if (isset($file['body']) === false || file_put_contents($output_pathname, (string)$file['body']) === false) {
                    throw new \Exception('Failed to write output file for: ' . $output_pathname);
                }
            } else if (copy($file->getPathname(), $output_pathname) === false) {
                throw new \Exception('Failed to write output file for: ' . $output_pathname);
            }
            if (chmod($output_pathname, $this->file_mode) === false) {
                throw new \Exception('Failed to change file mode for: ' . $output_pathname);
            }
            if ($this->owner && chown($output_pathname, $this->owner) === false) {
                throw new \Exception('Failed to change owner for file: ' . $output_pathname);
            }
            if ($this->group && chgrp($output_pathname, $this->group) === false) {
                throw new \Exception('Failed to change group for file: ' . $output_pathname);
            }
        }
    }
}
