<?php
declare(strict_types=1);

namespace App\Contracts;

interface FileServiceInterface
{
    public function loadText(string $path): string;
    public function saveText(string $path, string $content): void;
    public function loadJSON(string $path): array;
    public function saveJSON(string $path, array $json): void;
    public function exists(string $path): bool;
    public function copy(string $source, string $destination, bool $overwrite): void;
}

