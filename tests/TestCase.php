<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\APIToolKitServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            APIToolKitServiceProvider::class,
        ];
    }
}
