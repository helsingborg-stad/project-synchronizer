<?php

declare(strict_types=1);

namespace App\Services\Tests;

use App\Services\ConfigService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ConfigServiceTest extends TestCase
{
    private $config = null;

    protected function setUp(): void
    {
        $this->config = new ConfigService((object) []);

        $fs = $this->createMock(\App\Contracts\FileServiceInterface::class);

        $fs->method('loadJSON')->willReturn([
            // Not an array
            'invalid_value' => null,
            // Not a leading slash
            'invalid_path' => [],
            // Not a list
            'invalid_list' => ['key' => 'value'],
        ]);

        $this->config->loadFiles($fs);
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(ConfigService::class, $this->config);
    }

    #[TestDox('Config is normalized when loaded')]
    public function testConfigIsLoaded()
    {
        $this->assertEquals(['/invalid_path' => []], $this->config->getFiles());
    }
}
