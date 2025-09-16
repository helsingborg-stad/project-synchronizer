<?php
declare(strict_types=1);

namespace App\Services\Tests;

use App\Transforms\Transform;
use App\Services\NullLoggerService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class TransformTest extends TestCase
{
    private $transform = null;

    protected function setUp(): void
    {
        $this->transform = new Transform(new NullLoggerService());
    }


    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(Transform::class, $this->transform);
    }

    #[TestDox('Add missing semver properties')]
    public function testAddMissingSemverProperties()
    {
        $result = $this->transform->transform(
            [
            // Reference
            "libraryA" => "1.0.0",
            ], [
            // Target
            "libraryB" => "2.0.0",
            ]
        );
        $this->assertEquals(
            [
            // Target after transform
            "libraryB" => "2.0.0",
            "libraryA" => "1.0.0",
            ], $result
        );
    }

    #[TestDox('Upgrade existing semver properties')]
    public function testUpgradeExistingSemverProperties()
    {
        $result = $this->transform->transform(
            [
            // Reference
            "libraryA" => "^2.0.1",
            "libraryB" => "1.0.0",
            ], [
            // Target
            "libraryA" => "1.0.0",
            "libraryB" => "2.0.0",
            ]
        );
        $this->assertEquals(
            [
            // Target after transform
            "libraryA" => "^2.0.1",
            "libraryB" => "2.0.0",
            ], $result
        );
    }

    #[TestDox('Avoid downgrading versions')]
    public function testAvoidDowngradingVersions()
    {
        $result = $this->transform->transform(
            [
            // Reference
            "libraryA" => "1.0.0",
            ], [
            // Target
            "libraryA" => "2.0.0",
            "libraryB" => "2.0.0",
            ]
        );
        $this->assertEquals(
            [
            // Target after transform
            "libraryA" => "2.0.0",
            "libraryB" => "2.0.0",
            ], $result
        );
    }
    
    #[TestDox('Add missing string properties')]
    public function testAddMissingStringProperties()
    {
        $result = $this->transform->transform(
            [
            // Reference
            "NameA" => "ValueA",
            ], [
            // Target
            "NameB" => "ValueB",
            ]
        );
        $this->assertEquals(
            [
            // Target after transform
            "NameB" => "ValueB",
            "NameA" => "ValueA",
            ], $result
        );
    }

    #[TestDox('Retain existing string properties')]
    public function testRetainStringProperties()
    {
        $result = $this->transform->transform(
            [
            // Reference
            "NameA" => "ValueA",
            ], [
            // Target
            "NameA" => "ValueA_Old",
            ]
        );
        $this->assertEquals(
            [
            // Target after transform
            "NameA" => "ValueA_Old",
            ], $result
        );
    }
    #[TestDox('Retain existing string value')]
    public function testRetainExistingStringValue()
    {
        $result = $this->transform->transform(
            // Reference
            "ValueA",
            // Target
            "ValueB"
        );
        $this->assertEquals(
            // Target after transform
            "ValueB",
            $result
        );
    }

    #[TestDox('Add missing string value')]
    public function testAddMissingStringValue()
    {
        $result = $this->transform->transform(
            // Reference
            "ValueA",
            // Target
            null
        );
        $this->assertEquals(
            // Target after transform
            "ValueA",
            $result
        );
    }

    #[TestDox('Update semver value')]
    public function testUpdateSemverValue()
    {
        $result = $this->transform->transform(
            // Reference
            "2.0.0",
            // Target
            "1.0.0"
        );
        $this->assertEquals(
            // Target after transform
            "2.0.0",
            $result
        );
    }

    #[TestDox('Merge sub-array values')]
    public function testMergeSubArrayValues()
    {
        $result = $this->transform->transform([
            // Reference
            ["ValueA", "ValueB"],
        ], [
            // Target
            ["ValueC", "ValueD"]
        ]);
        $this->assertEquals([
                // Target after transform
                ["ValueA", "ValueB", "ValueC", "ValueD"]
            ],
            $result
        );
    }

}