<?php

declare(strict_types=1);

namespace App\Services\Tests;

use App\Services\ConfigService;
use App\Transforms\Transform;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class TransformTest extends TestCase
{
    private $transform = null;
    private ConfigService $config;

    protected function setUp(): void
    {
        $this->transform = new Transform();
        $this->config = new ConfigService((object) []);
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(Transform::class, $this->transform);
    }

    #[TestDox('add missing name value pairs')]
    public function testAddMissingNameValuePairs()
    {
        $result = $this->transform->transform(
            [
                // Reference
                'libraryA' => '1.0.0',
            ],
            [
                // Target
                'libraryB' => '2.0.0',
            ],
            $this->config,
        );
        $this->assertEquals(
            [
                // Target after transform
                'libraryB' => '2.0.0',
                'libraryA' => '1.0.0',
            ],
            $result,
        );
    }

    #[TestDox('add nested name value pairs')]
    public function testAddNestedNameValuePairs()
    {
        $result = $this->transform->transform(
            [
                // Reference
                'libraryA' => ['propertyA' => 'ValueA'],
            ],
            [
                // Target
                'libraryA' => ['propertyB' => 'ValueB'],
            ],
            $this->config,
        );
        $this->assertEquals(
            [
                // Target after transform
                'libraryA' => ['propertyB' => 'ValueB', 'propertyA' => 'ValueA'],
            ],
            $result,
        );
    }

    #[TestDox('upgrade nested name value pairs (semver)')]
    public function testUpgradeNestedNameValuePairs()
    {
        $result = $this->transform->transform(
            [
                // Reference
                'libraryA' => ['propertyA' => '~2.0'],
            ],
            [
                // Target
                'libraryA' => ['propertyA' => '~1.0'],
            ],
            $this->config,
        );
        $this->assertEquals(
            [
                // Target after transform
                'libraryA' => ['propertyA' => '~2.0'],
            ],
            $result,
        );
    }

    #[TestDox('upgrade name value pairs (semver)')]
    public function testUpgradeNameValuePairs()
    {
        $result = $this->transform->transform(
            [
                // Reference
                'libraryA' => '^2.0.1',
                'libraryB' => '^0.26.1',
            ],
            [
                // Target
                'libraryA' => '1.0.0',
                'libraryB' => '^0.2',
            ],
            $this->config,
        );
        $this->assertEquals(
            [
                // Target after transform
                'libraryA' => '^2.0.1',
                'libraryB' => '^0.26.1',
            ],
            $result,
        );
    }

    #[TestDox('retain name value pairs if newer (semver)')]
    public function testRetainNewerVersions()
    {
        $result = $this->transform->transform(
            [
                // Reference
                'libraryA' => '1.0.0',
            ],
            [
                // Target
                'libraryA' => '2.0.0',
            ],
            $this->config,
        );
        $this->assertEquals(
            [
                // Target after transform
                'libraryA' => '2.0.0',
            ],
            $result,
        );
    }

    #[TestDox('overwrite name value pairs if newer (semver)')]
    public function testOverwriteNewerVersions()
    {
        $this->config->setForce(true);

        $result = $this->transform->transform(
            [
                // Reference
                'libraryA' => '1.0.0',
            ],
            [
                // Target
                'libraryA' => '2.0.0',
            ],
            $this->config,
        );
        $this->config->setForce(false);

        $this->assertEquals(
            [
                // Target after transform
                'libraryA' => '1.0.0',
            ],
            $result,
        );
    }

    #[TestDox('retain existing name value pairs')]
    public function testRetainNameValuePairs()
    {
        $result = $this->transform->transform(
            [
                // Reference
                'NameA' => 'ValueA_New',
                'NameB' => false,
                'NameC' => 2.0,
            ],
            [
                // Target
                'NameA' => 'ValueA',
                'NameB' => true,
                'NameC' => 1.0,
            ],
            $this->config,
        );
        $this->assertEquals(
            [
                // Target after transform
                'NameA' => 'ValueA',
                'NameB' => true,
                'NameC' => 1.0,
            ],
            $result,
        );
    }

    #[TestDox('retain single string value')]
    public function testRetainExistingStringValue()
    {
        $result = $this->transform->transform(
            // Reference
            'ValueA',
            // Target
            'ValueB',
            $this->config,
        );
        $this->assertEquals(
            // Target after transform
            'ValueB',
            $result,
        );
    }

    #[TestDox('add single string value')]
    public function testAddMissingStringValue()
    {
        $result = $this->transform->transform(
            // Reference
            'ValueA',
            // Target
            null,
            $this->config,
        );
        $this->assertEquals(
            // Target after transform
            'ValueA',
            $result,
        );
    }

    #[TestDox('upgrade single semver value')]
    public function testUpgradeSemverValue()
    {
        $result = $this->transform->transform(
            // Reference
            '2.0.0',
            // Target
            '1.0.0',
            $this->config,
        );
        $this->assertEquals(
            // Target after transform
            '2.0.0',
            $result,
        );
    }

    #[TestDox('merge arrays')]
    public function testArrayMerge()
    {
        $result = $this->transform->transform(
            // Reference
            ['ValueA', 'ValueB'],
            // Target
            ['ValueA', 'ValueC', 'ValueD'],
            $this->config,
        );
        $this->assertEquals(
            // Target after transform
            ['ValueA', 'ValueB', 'ValueC', 'ValueD'],
            $result,
        );
    }

    #[TestDox('merge arrays where target is missing')]
    public function testArrayWithMissingTarget()
    {
        $result = $this->transform->transform(
            // Reference
            ['ValueA', 'ValueB'],
            // Target
            null,
            $this->config,
        );
        $this->assertEquals(
            // Target after transform
            ['ValueA', 'ValueB'],
            $result,
        );
    }

    #[TestDox('apply object where target is missing')]
    public function testObjectWithMissingTarget()
    {
        $result = $this->transform->transform(
            // Reference
            ['ValueA' => 'ValueB'],
            // Target
            null,
            $this->config,
        );
        $this->assertEquals(
            // Target after transform
            ['ValueA' => 'ValueB'],
            $result,
        );
    }
}
