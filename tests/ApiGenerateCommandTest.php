<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Generator\Contracts\HasClassAndNamespace;
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
use Essa\APIToolKit\Generator\SchemaDefinition;
use Essa\APIToolKit\Generator\SchemaParsers\CreateValidationRulesParser;
use Essa\APIToolKit\Generator\SchemaParsers\FactoryColumnsParser;
use Essa\APIToolKit\Generator\SchemaParsers\MigrationContentParser;
use Essa\APIToolKit\Generator\SchemaParsers\RelationshipMethodsParser;
use Essa\APIToolKit\Generator\SchemaParsers\ResourceAttributesParser;
use Essa\APIToolKit\Generator\SchemaParsers\UpdateValidationRulesParser;
use Symfony\Component\Console\Command\Command;

class ApiGenerateCommandTest extends TestCase
{
    /**
     * @test
     */
    public function reservedNameValidation(): void
    {
        $this->artisan('api:generate', [
            'model' => 'class',
        ])
            ->expectsOutput('The name "class" is reserved by PHP.')
            ->assertExitCode(Command::FAILURE);
    }

    /**
     * @test
     * @dataProvider pathResolverProvider
     */
    public function generateCommandWithAllDefaults($resolverClass): void
    {
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            '--all' => true,
        ])->assertExitCode(Command::SUCCESS);

        $pathResolver = new $resolverClass($model);

        $this->assertFileExists($pathResolver->getFullPath());

        if ($pathResolver instanceof HasClassAndNamespace) {
            $this->assertStringContainsString(
                'namespace ' . $pathResolver->getNameSpace() . ';',
                file_get_contents($pathResolver->getFullPath())
            );
        }
    }

    public function pathResolverProvider(): array
    {
        return [
            [ModelPathResolver::class],
            [ControllerPathResolver::class],
            [ResourcePathResolver::class],
            [CreateFormRequestPathResolver::class],
            [UpdateFormRequestPathResolver::class],
            [FilterPathResolver::class],
            [SeederPathResolver::class],
            [FactoryPathResolver::class],
            [TestPathResolver::class],
            [RoutesPathResolver::class],
        ];
    }

    /**
     * @test
     */
    public function generateCommandWithSchemaShouldGenerateModelWithFillableAndRelations(): void
    {
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            'schema' => $schema = "username:string:default('ahmed')|email:string:unique|company_data_id:foreignId:cascadeOnDelete",
        ])
            ->assertExitCode(Command::SUCCESS);

        $generatedModelContent = file_get_contents((new ModelPathResolver($model))->getFullPath());

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines("protected \$fillable = [ 'username', 'email', 'company_data_id', ];"),
            $this->normalizeWhitespaceAndNewlines($generatedModelContent)
        );

        $this->assertStringContainsString(
            (new RelationshipMethodsParser(SchemaDefinition::createFromSchemaString($schema)))->parse(),
            $generatedModelContent
        );
    }

    /**
     * @test
     */
    public function generateCommandWithAllDefaultsAndSchemaShouldGenerateMigration(): void
    {
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            'schema' => $schema = "username:string:default('ahmed')|email:string:unique|company_id:foreignId:cascadeOnDelete",
            '--migration' => true,
        ])
            ->assertExitCode(Command::SUCCESS);

        $migrationContent = file_get_contents((new MigrationPathResolver($model))->getFullPath());

        $this->assertStringContainsString(
            (new MigrationContentParser(SchemaDefinition::createFromSchemaString($schema)))->parse(),
            $migrationContent
        );
    }

    /**
     * @test
     */
    public function generateCommandWithAllDefaultsAndSchemaShouldGenerateFactory(): void
    {
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            'schema' => $schema = "username:string:default('ahmed')|code:integer:unique|company_data_id:foreignId:cascadeOnDelete",
            '--factory' => true,
        ])
            ->assertExitCode(Command::SUCCESS);

        $factoryContent = file_get_contents((new FactoryPathResolver($model))->getFullPath());

        $this->assertStringContainsString(
            (new FactoryColumnsParser(SchemaDefinition::createFromSchemaString($schema)))->parse(),
            $factoryContent
        );
    }

    /**
     * @test
     */
    public function generateCommandWithAllDefaultsAndSchemaShouldGenerateResource(): void
    {
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            'schema' => $schema = "username:string:default('ahmed')|email:string:unique|company_data_id:foreignId:cascadeOnDelete",
            '--all' => true,
        ])
            ->assertExitCode(Command::SUCCESS);

        $resourceContent = file_get_contents((new ResourcePathResolver($model))->getFullPath());

        $this->assertStringContainsString(
            (new ResourceAttributesParser(SchemaDefinition::createFromSchemaString($schema)))->parse(),
            $resourceContent
        );
    }

    /**
     * @test
     */
    public function generateCommandWithAllDefaultsAndSchemaShouldGenerateRequests(): void
    {
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            'schema' => $schema = "username:string:default('ahmed')|email:string:unique|company_data_id:foreignId:cascadeOnDelete",
            '--request' => true,
        ])
            ->assertExitCode(Command::SUCCESS);

        $createRequestContent = file_get_contents((new CreateFormRequestPathResolver($model))->getFullPath());
        $updateRequestContent = file_get_contents((new UpdateFormRequestPathResolver($model))->getFullPath());

        $this->assertStringContainsString(
            (new CreateValidationRulesParser(SchemaDefinition::createFromSchemaString($schema)))->parse(),
            $createRequestContent
        );

        $this->assertStringContainsString(
            (new UpdateValidationRulesParser(SchemaDefinition::createFromSchemaString($schema)))->parse(),
            $updateRequestContent
        );
    }

    /**
     * @test
     */
    public function generateCommandWithoutDefaultOptionsButWithSoftDelete(): void
    {
        $model = 'CustomSoftDeleteModel';

        $this->artisan('api:generate', [
            'model' => 'CustomSoftDeleteModel',
            '--all' => true,
            '--soft-delete' => true,
        ])
            ->assertExitCode(Command::SUCCESS);

        $this->assertStringContainsString('SoftDeletes', file_get_contents((new ModelPathResolver($model))->getFullPath()));
        $this->assertStringContainsString('permanent-delete', file_get_contents((new RoutesPathResolver($model))->getFullPath()));
        $this->assertStringContainsString('restore', file_get_contents((new RoutesPathResolver($model))->getFullPath()));
        $this->assertStringContainsString('forceDelete', file_get_contents((new ControllerPathResolver($model))->getFullPath()));
    }

    public function generateCommandWithoutDefaultOptionsButWithoutSoftDelete(): void
    {
        $model = 'CustomModel';

        $this->artisan('api:generate', [
            'model' => 'CustomModel',
            '--all' => true,
        ])
            ->assertExitCode(Command::SUCCESS);

        $this->assertStringNotContainsString('SoftDeletes', file_get_contents((new ModelPathResolver($model))->getFullPath()));
        $this->assertStringNotContainsString('permanent-delete', file_get_contents((new RoutesPathResolver($model))->getFullPath()));
        $this->assertStringNotContainsString('restore', file_get_contents((new RoutesPathResolver($model))->getFullPath()));
        $this->assertStringNotContainsString('forceDelete', file_get_contents((new ControllerPathResolver($model))->getFullPath()));
    }

    /**
     * @test
     */
    public function generateCommandWithoutDefaultOptions(): void
    {
        $model = 'WithoutDefaultNewCustomModel';

        $this->artisan('api:generate', [
            'model' => 'WithoutDefaultNewCustomModel',
            '--soft-delete' => false,
            '--controller' => false,
            '--request' => false,
            '--resource' => true,
            '--migration' => true,
            '--factory' => false,
            '--seeder' => true,
            '--filter' => false,
            '--test' => true,
            '--routes' => true,
        ])
            ->assertExitCode(Command::SUCCESS);

        $this->assertFileExists((new ModelPathResolver($model))->getFullPath());
        $this->assertFileExists((new ResourcePathResolver($model))->getFullPath());
        $this->assertFileExists((new SeederPathResolver($model))->getFullPath());
        $this->assertFileExists((new TestPathResolver($model))->getFullPath());

        $this->assertFileDoesNotExist((new ControllerPathResolver($model))->getFullPath());
        $this->assertFileDoesNotExist((new FilterPathResolver($model))->getFullPath());
        $this->assertFileDoesNotExist((new FactoryPathResolver($model))->getFullPath());
    }
}
