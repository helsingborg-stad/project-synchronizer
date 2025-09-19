<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\LoggerServiceInterface;

class ConsoleLoggerService implements LoggerServiceInterface
{
    public function write(string $row): void
    {
        echo $row . PHP_EOL;
    }
}
