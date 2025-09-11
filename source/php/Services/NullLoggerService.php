<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\LoggerServiceInterface;

class NullLoggerService implements LoggerServiceInterface
{
    public function write(string $row): void
    {
    }
}