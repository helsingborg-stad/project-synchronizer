<?php
declare(strict_types=1);

namespace App\Services\Tests;

use App\Services\ConfigService;
use App\Services\NullLoggerService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ConfigServiceTest extends TestCase
{
    private $config = null;

    protected function setUp(): void
    {
        $this->config = new ConfigService(
            [
                "invalid_value" => null,
                "invalid_path" => [],
                "invalid_object" => ["key" => "value"],
            ],
            new NullLoggerService()
        );
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(ConfigService::class, $this->config);
    }

    #[TestDox('Config is normalized when loaded')]
    public function testConfigIsLoaded()
    {
        $this->assertEquals(
            ["/invalid_path" => []], $this->config->getFiles()
        );
    }
}