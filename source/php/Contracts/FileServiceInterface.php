<?php

declare(strict_types=1);

namespace App\Contracts;

interface FileServiceInterface
{
    public function createDirectory(string $path): void;

    public function loadText(string $path): string;

    public function saveText(string $path, string $content, bool $overwrite): void;

    public function loadJSON(string $path): array;

    public function saveJSON(string $path, array $json, bool $overwrite): void;

    public function exists(string $path): bool;

    public function copy(string $source, string $destination, bool $overwrite): void;
}
