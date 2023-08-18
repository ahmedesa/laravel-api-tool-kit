<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Tests\Mocks\TestEnum;

class EnumTest extends TestCase
{
    /**
     * @test
     */
    public function generatesEnumClass(): void
    {
        $name = 'TestEnum';

        $this->artisan('make:enum', ['name' => $name])
            ->assertExitCode(0);

        $actionClassPath = app_path("Enums/{$name}.php");

        $this->assertFileExists($actionClassPath);

        $this->assertStringContainsString('namespace App\\Enums;', file_get_contents($actionClassPath));

        $this->assertStringContainsString("class {$name}", file_get_contents($actionClassPath));
    }

    /** @test */
    public function itProvidesEnumFunctionality(): void
    {
        $this->assertEquals(['EXAMPLE' => 'example'], TestEnum::toArray());
        $this->assertEquals(['EXAMPLE'], TestEnum::getConst());
        $this->assertTrue(TestEnum::isValid('example'));
        $this->assertTrue(TestEnum::isValidConst('EXAMPLE'));
        $this->assertEquals('example', TestEnum::getValue('EXAMPLE'));
    }
}
