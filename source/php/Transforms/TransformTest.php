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

    #[TestDox('Add semver properties')]
    public function testAddSemverProperties()
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

    #[TestDox('Update semver properties')]
    public function testUpdateSemverProperties()
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

    #[TestDox('Ignore existing properties')]
    public function testIgnoreProperties()
    {
        $result = $this->transform->transform(
            [
            // Reference
            "libraryA" => "2.0.0",
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
    
    #[TestDox('Add string properties')]
    public function testAddStringProperties()
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

    #[TestDox('Update string properties')]
    public function testUpdateStringProperties()
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
            "NameA" => "ValueA",
            ], $result
        );
    }
    #[TestDox('Add or update string value')]
    public function testAddOrUpdateStringValue()
    {
        $result = $this->transform->transform(
            // Reference
            "ValueA",
            // Target
            "ValueB"
        );
        $this->assertEquals(
            // Target after transform
            "ValueA",
            $result
        );
    }
}