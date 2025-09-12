<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\FileServiceInterface;

class NullFileService implements FileServiceInterface
{
    
    public function fetchRemoteFile(string $repo, string $path): array
    {
        return [
            "null" => null
        ];
    }

    public function fetchLocalFile(string $path): array
    {
        return [
            "null" => null
        ];
    }

    public function saveLocalFile(string $path, array $content): void
    {
    }
}

