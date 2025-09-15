<?php
declare(strict_types=1);

namespace App\Services\Tests;

use App\Transforms\Transform;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class TransformTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $transform = new Transform();
        $this->assertInstanceOf(Transform::class, $transform);
    }

    #[TestDox('Add semver properties')]
    public function testAddSemverProperties()
    {
        $transform = new Transform();

        $result = $transform->transform(
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
        $transform = new Transform();

        $result = $transform->transform(
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
        $transform = new Transform();

        $result = $transform->transform(
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
        $transform = new Transform();

        $result = $transform->transform(
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
        $transform = new Transform();

        $result = $transform->transform(
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
        $transform = new Transform();

        $result = $transform->transform(
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