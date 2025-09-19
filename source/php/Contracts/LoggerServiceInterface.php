<?php

declare(strict_types=1);

namespace App\Contracts;

interface LoggerServiceInterface
{
    public function write(string $row): void;
}
