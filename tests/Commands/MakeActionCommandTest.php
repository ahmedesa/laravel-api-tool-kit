<?php

namespace Essa\APIToolKit\Tests\Commands;

use Essa\APIToolKit\Tests\TestCase;

class MakeActionCommandTest extends TestCase
{
    /**
     * @test
     */
    public function generateAction()
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
