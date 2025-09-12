<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\FileServiceInterface;

class FileService implements FileServiceInterface
{
    protected function fromJson($content): array {
        $json = json_decode($content, true);
        if($json === null) {
            throw new \Exception("Invalid JSON content");
        }
        return $json;
    }
    
    public function fetchRemoteFile(string $repo, string $path): array {
        $content = @file_get_contents($repo . $path);
        if ($content === false) {
            throw new \Exception("Could not fetch remote file: $path");
        }
        return $this->fromJson($content);
    }

    public function fetchLocalFile(string $path): array
    {
        if (!file_exists($path)) {
            throw new \Exception("Local file does not exist: $path");
        }
        $content = @file_get_contents($path);
        if ($content === false) {
            throw new \Exception("Could not read local file: $path");
        }
        return $this->fromJson($content);
    }

    public function saveLocalFile(string $path, array $content): void {
        $jsonContent = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($jsonContent === false) {
            throw new \Exception("Could not encode content to JSON");
        }
        $result = @file_put_contents($path, $jsonContent);
        if ($result === false) {
            throw new \Exception("Could not write to local file: $path");
        }
    }
}

