<?php

declare(strict_types=1);

namespace App\Services\Tests;

use App\Services\FileService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class FileServiceTest extends TestCase
{
    private FileService $service;

    protected function setUp(): void
    {
        $this->service = new FileService();
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(FileService::class, $this->service);
    }

    #[TestDox('throws exception on invalid file')]
    public function testThrowsExceptionOnInvalidFile()
    {
        $this->expectException(\Exception::class);
        $this->service->loadJSON('/path/to/nonexistent/file.json');
    }

    #[TestDox('throws exception on existing file')]
    public function testThrowsExceptionOnExistingFile()
    {
        $this->expectException(\Exception::class);
        $this->service->copy(__FILE__, __FILE__, false);
    }
}
