<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\FileServiceInterface;

class FileService implements FileServiceInterface
{
    public function loadText(string $path): string
    {
        $content = @file_get_contents($path);
        if ($content === false) {
            throw new \Exception("Failed to read from file: $path");
        }
        return $content;
    }

    public function saveText(string $path, string $content): void
    {
        $result = @file_put_contents($path, $content);
        if ($result === false) {
            throw new \Exception("Failed to write to file: $path");
        }
    }
    public function loadJSON(string $path): array
    {
        $json = json_decode($this->loadText($path), true);
        if($json === null) {
            throw new \Exception("Failed to decode JSON from file: $path");
        }
        return $json;
    }

    public function saveJSON(string $path, array $json): void
    {
        $content = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($content === false) {
            throw new \Exception("Failed to encode JSON for file: $path");
        }
        $this->saveText($path, $content);
    }

    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function copy(string $source, string $destination, bool $overwrite): void {
        if (!$overwrite && $this->exists($destination)) {
            throw new \Exception("File already exists: $destination");
        }
        $this->saveText($destination, $this->loadText($source));
    }
}

