<?php

declare(strict_types=1);

namespace App\Contracts;

interface ConfigServiceInterface
{
    public function getFiles(): array;

    public function loadFiles(FileServiceInterface $fs): void;

    public function getSourcePath(): string;

    public function getTargetPath(): string;

    public function getConfigPath(): string;

    public function getForce(): bool;
}
