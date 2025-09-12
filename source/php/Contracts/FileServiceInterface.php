<?php
declare(strict_types=1);

namespace App\Contracts;

interface FileServiceInterface
{
    public function load(string $path): array;
    public function save(string $path, array $content): void;
}

