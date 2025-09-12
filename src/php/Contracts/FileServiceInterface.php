<?php
declare(strict_types=1);

namespace App\Contracts;

interface FileServiceInterface
{
    public function fetchRemoteFile(string $repo, string $path): array;
    public function fetchLocalFile(string $path): array;
    public function saveLocalFile(string $path, array $content): void;
}

