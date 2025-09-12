<?php
declare(strict_types=1);

namespace App\Services\Tests;

use App\Services\FileService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class FileServiceTest extends TestCase {
    private FileService $service;

    protected function setUp(): void {
        $this->service = new FileService();
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated() {
        $this->assertInstanceOf(FileService::class, $this->service);
    }

    #[TestDox('throws exception on invalid remote file')]
    public function testThrowsExceptionOnInvalidRemoteFile() {
        $this->expectException(\Exception::class);
        $this->service->fetchRemoteFile('invalid-repo', '/invalid-path');
    }

    #[TestDox('throws exception on invalid local file')]
    public function testThrowsExceptionOnInvalidLocalFile() {
        $this->expectException(\Exception::class);
        $this->service->fetchLocalFile('/invalid-path');
    }
}