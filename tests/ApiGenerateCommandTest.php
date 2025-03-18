<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Enum\GeneratorFilesType;
use Essa\APIToolKit\Generator\Configs\PathConfigHandler;
use Essa\APIToolKit\Generator\GeneratedFileInfo;
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
    public static function generatedTypeProvider(): array
    {
        return [
            [GeneratorFilesType::MODEL],
            [GeneratorFilesType::CONTROLLER],
            [GeneratorFilesType::RESOURCE],
            [GeneratorFilesType::FACTORY],
            [GeneratorFilesType::SEEDER],
            [GeneratorFilesType::TEST],
            [GeneratorFilesType::FILTER],
            [GeneratorFilesType::MIGRATION],
            [GeneratorFilesType::ROUTES],
            [GeneratorFilesType::CREATE_REQUEST],
            [GeneratorFilesType::UPDATE_REQUEST],
        ];
    }

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
     */
    public function invalidGroupName(): void
    {
        $this->artisan('api:generate', [
            'model' => 'newModel',
            '--group' => 'not-exist',
        ])
            ->expectsOutput('The path group you entered is not valid')
            ->assertExitCode(Command::FAILURE);
    }

    /**
     * @test
     * @dataProvider generatedTypeProvider
     */
    public function generateCommandWithAllDefaults(string $fileType): void
    {
        $model = 'GeneratedModel';

        $this->artisan('api:generate', [
            'model' => $model,
            '--all' => true,
        ])->assertExitCode(Command::SUCCESS);

        $generatedFilePath = PathConfigHandler::generateFilePathInfo('default', $fileType, $model);

        $this->assertFileExists($generatedFilePath->getFullPath());

        if ($generatedFilePath->getNamespace()) {
            $this->assertStringContainsString(
                'namespace ' . $generatedFilePath->getNameSpace() . ';',
                file_get_contents($generatedFilePath->getFullPath())
            );
        }
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

        $generatedModelContent = $this->getFileContentsForType($model, GeneratorFilesType::MODEL);

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

        $migrationContent = $this->getFileContentsForType($model, GeneratorFilesType::MIGRATION);

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

        $factoryContent = $this->getFileContentsForType($model, GeneratorFilesType::FACTORY);

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

        $resourceContent = $this->getFileContentsForType($model, GeneratorFilesType::RESOURCE);

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

        $createRequestContent = $this->getFileContentsForType($model, GeneratorFilesType::CREATE_REQUEST);
        $updateRequestContent = $this->getFileContentsForType($model, GeneratorFilesType::UPDATE_REQUEST);

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

        $this->assertStringContainsString('SoftDeletes', $this->getFileContentsForType($model, GeneratorFilesType::MODEL));
        $this->assertStringContainsString('permanent-delete', $this->getFileContentsForType($model, GeneratorFilesType::ROUTES));
        $this->assertStringContainsString('restore', $this->getFileContentsForType($model, GeneratorFilesType::ROUTES));
        $this->assertStringContainsString('forceDelete', $this->getFileContentsForType($model, GeneratorFilesType::CONTROLLER));
    }

    public function generateCommandWithoutDefaultOptionsButWithoutSoftDelete(): void
    {
        $model = 'CustomModel';

        $this->artisan('api:generate', [
            'model' => 'CustomModel',
            '--all' => true,
        ])
            ->assertExitCode(Command::SUCCESS);

        $this->assertStringNotContainsString('SoftDeletes', $this->getFileContentsForType($model, GeneratorFilesType::MODEL));
        $this->assertStringNotContainsString('permanent-delete', $this->getFileContentsForType($model, GeneratorFilesType::ROUTES));
        $this->assertStringNotContainsString('restore', $this->getFileContentsForType($model, GeneratorFilesType::ROUTES));
        $this->assertStringNotContainsString('forceDelete', $this->getFileContentsForType($model, GeneratorFilesType::CONTROLLER));
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

        $this->assertFileExists($this->getGeneratedFilePathForDefaultGroup($model, GeneratorFilesType::MODEL)->getFullPath());
        $this->assertFileExists($this->getGeneratedFilePathForDefaultGroup($model, GeneratorFilesType::RESOURCE)->getFullPath());
        $this->assertFileExists($this->getGeneratedFilePathForDefaultGroup($model, GeneratorFilesType::SEEDER)->getFullPath());
        $this->assertFileExists($this->getGeneratedFilePathForDefaultGroup($model, GeneratorFilesType::TEST)->getFullPath());

        $this->assertFileDoesNotExist($this->getGeneratedFilePathForDefaultGroup($model, GeneratorFilesType::CONTROLLER)->getFullPath());
        $this->assertFileDoesNotExist($this->getGeneratedFilePathForDefaultGroup($model, GeneratorFilesType::FILTER)->getFullPath());
        $this->assertFileDoesNotExist($this->getGeneratedFilePathForDefaultGroup($model, GeneratorFilesType::FACTORY)->getFullPath());
    }

    private function getFileContentsForType(string $model, string $type): string|false
    {
        return file_get_contents($this->getGeneratedFilePathForDefaultGroup($model, $type)->getFullPath());
    }

    private function getGeneratedFilePathForDefaultGroup(string $model, string $type): GeneratedFileInfo
    {
        return PathConfigHandler::generateFilePathInfo('default', $type, $model);
    }
}
