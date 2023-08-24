<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Generator\PathResolver\ControllerPathResolver;
use Essa\APIToolKit\Generator\PathResolver\CreateFormRequestPathResolver;
use Essa\APIToolKit\Generator\PathResolver\FactoryPathResolver;
use Essa\APIToolKit\Generator\PathResolver\FilterPathResolver;
use Essa\APIToolKit\Generator\PathResolver\MigrationPathResolver;
use Essa\APIToolKit\Generator\PathResolver\ModelPathResolver;
use Essa\APIToolKit\Generator\PathResolver\ResourcePathResolver;
use Essa\APIToolKit\Generator\PathResolver\RoutesPathResolver;
use Essa\APIToolKit\Generator\PathResolver\SeedPathResolver;
use Essa\APIToolKit\Generator\PathResolver\TestPathResolver;
use Essa\APIToolKit\Generator\PathResolver\UpdateFormRequestPathResolver;

class PathResolverTest extends TestCase
{
    public function testModelPathResolver(): void
    {
        $model = 'User';
        $resolver = new ModelPathResolver($model);

        $expectedPath = app_path('/Models/User.php');
        $this->assertEquals($expectedPath, $resolver->getFullPath());
    }

    public function testControllerPathResolver(): void
    {
        $model = 'User';
        $resolver = new ControllerPathResolver($model);

        $expectedPath = app_path('Http/Controllers/API/UserController.php');
        $this->assertEquals($expectedPath, $resolver->getFullPath());
    }

    public function testCreateFormRequestPathResolver(): void
    {
        $model = 'User';
        $resolver = new CreateFormRequestPathResolver($model);

        $expectedPath = app_path("Http/Requests/User/CreateUserRequest.php");
        $this->assertEquals($expectedPath, $resolver->getFullPath());
    }

    public function testFactoryPathResolver(): void
    {
        $model = 'User';
        $resolver = new FactoryPathResolver($model);

        $expectedPath = database_path('/factories/UserFactory.php');
        $this->assertEquals($expectedPath, $resolver->getFullPath());
    }

    public function testFilterPathResolver(): void
    {
        $model = 'User';
        $resolver = new FilterPathResolver($model);

        $expectedPath = app_path('Filters/UserFilters.php');
        $this->assertEquals($expectedPath, $resolver->getFullPath());
    }

    public function testMigrationPathResolver(): void
    {
        $model = 'User';
        $resolver = new MigrationPathResolver($model);

        $expectedPath = database_path("migrations/" . date('Y_m_d_His') . "_create_users_table.php");

        $this->assertEquals($expectedPath, $resolver->getFullPath());
    }

    public function testResourcePathResolver(): void
    {
        $model = 'User';
        $resolver = new ResourcePathResolver($model);

        $expectedPath = app_path("Http/Resources/User/UserResource.php");
        $this->assertEquals($expectedPath, $resolver->getFullPath());
    }

    public function testRoutesPathResolver(): void
    {
        $model = 'User';
        $resolver = new RoutesPathResolver($model);

        $expectedPath = base_path('routes/api.php');
        $this->assertEquals($expectedPath, $resolver->getFullPath());
    }

    public function testSeedPathResolver(): void
    {
        $model = 'User';
        $resolver = new SeedPathResolver($model);

        $expectedPath = database_path('/seeders/UserSeeder.php');
        $this->assertEquals($expectedPath, $resolver->getFullPath());
    }

    public function testTestPathResolver(): void
    {
        $model = 'User';
        $resolver = new TestPathResolver($model);

        $expectedPath = base_path('tests/Feature/UserTest.php');
        $this->assertEquals($expectedPath, $resolver->getFullPath());
    }

    public function testUpdateFormRequestPathResolver(): void
    {
        $model = 'User';
        $resolver = new UpdateFormRequestPathResolver($model);

        $expectedPath = app_path("Http/Requests/User/UpdateUserRequest.php");
        $this->assertEquals($expectedPath, $resolver->getFullPath());
    }
}
