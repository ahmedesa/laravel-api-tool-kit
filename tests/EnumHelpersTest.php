<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Tests\Mocks\TestBackedEnum;

class EnumHelpersTest extends TestCase
{
    /** @test */
    public function itReturnsAllValues(): void
    {
        $this->assertEquals(['active', 'inactive', 'pending'], TestBackedEnum::values());
    }

    /** @test */
    public function itReturnsAllNames(): void
    {
        $this->assertEquals(['Active', 'Inactive', 'Pending'], TestBackedEnum::names());
    }

    /** @test */
    public function itConvertsToAssociativeArray(): void
    {
        $expected = [
            'Active' => 'active',
            'Inactive' => 'inactive',
            'Pending' => 'pending',
        ];

        $this->assertEquals($expected, TestBackedEnum::toArray());
    }

    /** @test */
    public function itValidatesEnumValues(): void
    {
        $this->assertTrue(TestBackedEnum::isValid('active'));
        $this->assertTrue(TestBackedEnum::isValid('inactive'));
        $this->assertTrue(TestBackedEnum::isValid('pending'));
        $this->assertFalse(TestBackedEnum::isValid('unknown'));
        $this->assertFalse(TestBackedEnum::isValid(''));
        $this->assertFalse(TestBackedEnum::isValid(123));
    }

    /** @test */
    public function itReturnsEnumCaseFromValue(): void
    {
        $this->assertSame(TestBackedEnum::Active, TestBackedEnum::fromValue('active'));
        $this->assertSame(TestBackedEnum::Inactive, TestBackedEnum::fromValue('inactive'));
        $this->assertNull(TestBackedEnum::fromValue('nonexistent'));
    }
}
