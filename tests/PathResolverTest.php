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
use Essa\APIToolKit\Generator\PathResolver\SeederPathResolver;
use Essa\APIToolKit\Generator\PathResolver\TestPathResolver;
use Essa\APIToolKit\Generator\PathResolver\UpdateFormRequestPathResolver;

class PathResolverTest extends TestCase
{
    /**
     * @test
     */
    public function ModelPathResolver(): void
    {
        $model = 'User';
        $resolver = new ModelPathResolver($model);

        $this->assertEquals(app_path('Models/User.php'), $resolver->getFullPath());
        $this->assertEquals('User', $resolver->getClassName());
        $this->assertEquals('App\Models', $resolver->getNameSpace());
    }

    /**
     * @test
     */
    public function ControllerPathResolver(): void
    {
        $model = 'User';
        $resolver = new ControllerPathResolver($model);

        $this->assertEquals(app_path('Http/Controllers/API/UserController.php'), $resolver->getFullPath());
        $this->assertEquals('UserController', $resolver->getClassName());
    }

    /**
     * @test
     */
    public function CreateFormRequestPathResolver(): void
    {
        $model = 'User';
        $resolver = new CreateFormRequestPathResolver($model);

        $expectedPath = app_path('Http/Requests/User/CreateUserRequest.php');
        $this->assertEquals($expectedPath, $resolver->getFullPath());
        $this->assertEquals('CreateUserRequest', $resolver->getClassName());
    }

    /**
     * @test
     */
    public function FactoryPathResolver(): void
    {
        $model = 'User';
        $resolver = new FactoryPathResolver($model);

        $expectedPath = database_path('factories/UserFactory.php');
        $this->assertEquals($expectedPath, $resolver->getFullPath());
        $this->assertEquals('UserFactory', $resolver->getClassName());
    }

    /**
     * @test
     */
    public function FilterPathResolver(): void
    {
        $model = 'User';
        $resolver = new FilterPathResolver($model);

        $this->assertEquals(app_path('Filters/UserFilters.php'), $resolver->getFullPath());
        $this->assertEquals('UserFilters', $resolver->getClassName());
    }

    /**
     * @test
     */
    public function MigrationPathResolver(): void
    {
        $model = 'User';
        $resolver = new MigrationPathResolver($model);

        $this->assertEquals(database_path('migrations/' . date('Y_m_d_His') . '_create_users_table.php'), $resolver->getFullPath());
    }

    /**
     * @test
     */
    public function ResourcePathResolver(): void
    {
        $model = 'User';
        $resolver = new ResourcePathResolver($model);

        $this->assertEquals(app_path('Http/Resources/User/UserResource.php'), $resolver->getFullPath());
        $this->assertEquals('UserResource', $resolver->getClassName());
    }

    /**
     * @test
     */
    public function RoutesPathResolver(): void
    {
        $model = 'User';
        $resolver = new RoutesPathResolver($model);

        $this->assertEquals(base_path('routes/api.php'), $resolver->getFullPath());
    }

    /**
     * @test
     */
    public function SeedPathResolver(): void
    {
        $model = 'User';
        $resolver = new SeederPathResolver($model);

        $this->assertEquals(database_path('seeders/UserSeeder.php'), $resolver->getFullPath());
        $this->assertEquals('UserSeeder', $resolver->getClassName());
    }

    /**
     * @test
     */
    public function TestPathResolver(): void
    {
        $model = 'User';
        $resolver = new TestPathResolver($model);

        $this->assertEquals(base_path('tests/Feature/UserTest.php'), $resolver->getFullPath());
        $this->assertEquals('UserTest', $resolver->getClassName());
    }

    /**
     * @test
     */
    public function UpdateFormRequestPathResolver(): void
    {
        $model = 'User';
        $resolver = new UpdateFormRequestPathResolver($model);

        $this->assertEquals(app_path('Http/Requests/User/UpdateUserRequest.php'), $resolver->getFullPath());
        $this->assertEquals('UpdateUserRequest', $resolver->getClassName());
    }
}
