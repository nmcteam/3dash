<?php
namespace Nmc\Ssg;

class IndexFiles implements PluginInterface
{
    public function handle(object $payload)
    {
        $root_path = $payload->files_path->getRealPath();
        $directory = new \RecursiveDirectoryIterator($root_path, \FilesystemIterator::FOLLOW_SYMLINKS);
        $filter = new \RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) {
            // Skip hidden files and directories.
            if ($current->getFilename()[0] === '.') {
                return false;
            }

            // Otherwise recurse all files and dirs
            return true;
        });
        $iterator = new \RecursiveIteratorIterator($filter);

        // Iterate content directory and load files into app payload
        foreach ($iterator as $info) {
            // Only parse files
            if ($info->isFile() === false || $info->isReadable() === false) {
                continue;
            }

            // Validate file path
            $file_path = $info->getRealPath();
            if (stripos($file_path, $root_path) !== 0) {
                throw new \Exception('Found invalid file path: ' . $file_path);
            }
            $file_path_under_root = substr_replace($file_path, '', 0, strlen($root_path));

            // Read file content
            $file_pathname = $info->getPathname();
            $file_content = file_get_contents($file_pathname);
            if ($file_content === false) {
                throw new \Exception('Failed to read file: ' . $file_pathname);
            }

            // Split file content into header and body
            $file_parts = preg_split("#(\r?\n){1,}---(\r?\n){1,}#", $file_content, 2);
            if (empty($file_parts)) {
                throw new \Exception('Found invalid file format: ' . $file_pathname);
            }

            // Add file to payload
            $header = '';
            $body = '';
            if (count($file_parts) === 1) {
                $body = $file_parts[0];
            } else {
                $header = $file_parts[0];
                $body = $file_parts[1];
            }
            $payload->files[$file_path_under_root] = new File(
                $file_pathname,
                $header,
                $body
            );
        }
    }
}
