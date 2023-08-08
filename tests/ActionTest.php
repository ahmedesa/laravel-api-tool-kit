<?php

namespace Essa\APIToolKit\Tests;

class ActionTest extends TestCase
{
    /**
     * @test
     */
    public function generateActionClass()
    {
        $name = 'TestAction';

        $this->artisan('make:action', ['name' => $name])
            ->assertExitCode(0);

        $actionClassPath = app_path("Actions/{$name}.php");

        $this->assertFileExists($actionClassPath);

        $this->assertStringContainsString('namespace App\\Actions;', file_get_contents($actionClassPath));

        $this->assertStringContainsString("class {$name}", file_get_contents($actionClassPath));

        $this->assertStringContainsString('public function execute(array $data)', file_get_contents($actionClassPath));
    }
}
