<?php
declare(strict_types=1);

namespace App\Contracts;

interface ConfigServiceInterface
{
    public function getConfig(): array;
    public function getRemoteRepoPath(): string;
}

