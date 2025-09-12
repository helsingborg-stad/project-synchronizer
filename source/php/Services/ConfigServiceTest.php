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
        $this->config = new ConfigService([
            "null" => null
        ]);
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
            [
            "null" => []
            ], $this->config->getConfig()
        );
    }

}