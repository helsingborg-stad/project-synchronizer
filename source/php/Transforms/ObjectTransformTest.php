<?php
declare(strict_types=1);

namespace App\Services\Tests;

use App\Transforms\ObjectTransform;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ObjectTransformTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $transform = new ObjectTransform();
        $this->assertInstanceOf(ObjectTransform::class, $transform);
    }

    #[TestDox('Add properties')]
    public function testAddProperties()
    {
        $transform = new ObjectTransform();

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

    #[TestDox('Update properties')]
    public function testUpdateProperties()
    {
        $transform = new ObjectTransform();

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
    #[TestDox('Add or update value')]
    public function testAddOrUpdateValue()
    {
        $transform = new ObjectTransform();

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