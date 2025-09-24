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
            'source' => 's1',
            'target' => 't1',
            'force' => true,
            'files' => [
                // Not an array
                'invalid_value' => null,
                // Not a leading slash
                'invalid_path' => [],
                // Not a list
                'invalid_list' => ['key' => 'value'],
            ],
        ]);

        $this->config->loadConfig($fs);
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(ConfigService::class, $this->config);
    }

    #[TestDox('config is normalized when loaded')]
    public function testConfigIsLoaded()
    {
        $this->assertEquals(['/invalid_path' => []], $this->config->getFiles());
    }

    #[TestDox('command line parameters read from config')]
    public function testCmdparametersThroughConfig()
    {
        $this->assertEquals('s1', $this->config->getSourcePath());
        $this->assertEquals('t1', $this->config->getTargetPath());
        $this->assertEquals(true, $this->config->getForce());
    }
}
