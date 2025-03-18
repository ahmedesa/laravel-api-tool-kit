<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Generator\Exception\SchemaNotValidException;
use Essa\APIToolKit\Generator\SchemaDefinition;
use Essa\APIToolKit\Generator\SchemaParsers\CreateValidationRulesParser;
use Essa\APIToolKit\Generator\SchemaParsers\FactoryColumnsParser;
use Essa\APIToolKit\Generator\SchemaParsers\FillableColumnsParser;
use Essa\APIToolKit\Generator\SchemaParsers\MigrationContentParser;
use Essa\APIToolKit\Generator\SchemaParsers\RelationshipMethodsParser;
use Essa\APIToolKit\Generator\SchemaParsers\ResourceAttributesParser;
use Essa\APIToolKit\Generator\SchemaParsers\UpdateValidationRulesParser;

class SchemaParserTest extends TestCase
{
    public static function schemasProvider(): array
    {
        return [
            ['column1:string|column2:integer|column3:datetime', true],
            ['COLUMN1:string|COLUMN2:int', true],
            ['COLUMN1:string|COLUMN2:int,COLUMN3:varchar(255)', true],
            ['COLUMN1:string', true],
            ["COLUMN1:int|COLUMN2:decimal(10,2)|COLUMN3:varchar(255):default('a')", true],
            ['COLUMN_NAME::OPTIONS', false], // Empty COLUMN_TYPE
            ['COLUMN1:', false],            // Empty COLUMN_TYPE with OPTIONS
            ['COLUMN1:int|:varchar(255)', false], // Empty COLUMN_NAME
            ['COLUMN1:string||COLUMN2:int', false], // Empty COLUMN_TYPE between two pipes
            ['COLUMN1:int|COLUMN2:decimal(10,2)|COLUMN3:varchar(255)"quote"', false], // With double quotes in OPTIONS
        ];
    }

    /**
     * @test
     */
    public function GenerateFillableColumns(): void
    {
        $schema = 'name:string|age:integer|email:string:unique|status:enum(approved,rejected)';
        $schemaParser = new FillableColumnsParser(SchemaDefinition::createFromSchemaString($schema));
        $output = $schemaParser->parse();

        $expectedFillableColumns = "
            'name',
            'age',
            'email',
            'status',
        ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedFillableColumns),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    /**
     * @test
     */
    public function ParseGeneratesMigrationContent(): void
    {
        $schema = 'name:string|age:integer';
        $schemaParser = new MigrationContentParser(SchemaDefinition::createFromSchemaString($schema));
        $output = $schemaParser->parse();

        $expectedMigrationContent = "
            \$table->string('name');
            \$table->integer('age');
        ";

        $this->assertEquals(
            $this->normalizeWhitespaceAndNewlines($expectedMigrationContent),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    /**
     * @test
     */
    public function GenerateRelationshipMethod(): void
    {
        $schema = 'author_id:foreignId|category_id:foreignId';
        $schemaParser = new RelationshipMethodsParser(SchemaDefinition::createFromSchemaString($schema));
        $output = $schemaParser->parse();

        $expectedMethod = "
            public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
            {
                return \$this->belongsTo(\App\Models\\Author::class);
            }
            public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
            {
                return \$this->belongsTo(\App\Models\Category::class);
            }
        ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedMethod),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    /**
     * @test
     */
    public function ParseGeneratesFactoryColumns(): void
    {
        $schema = 'name:string|age:integer|price:decimal|user_id:foreignId|status:enum(approved,rejected)';
        $schemaParser = new FactoryColumnsParser(SchemaDefinition::createFromSchemaString($schema));
        $output = $schemaParser->parse();

        $expectedFactoryContent = "
        'name' => \$this->faker->firstName(),
        'age' => \$this->faker->randomNumber(),
        'price' => \$this->faker->randomFloat(),
        'user_id' =>  createOrRandomFactory(\App\Models\User::class),
        'status' => \$this->faker->randomElement(['approved', 'rejected']),
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedFactoryContent),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    /**
     * @test
     */
    public function ParseGeneratesColumnDefinitions(): void
    {
        $schema = 'name:string|age:integer|price:decimal|status:enum(approved,rejected)';
        $schemaParser = new MigrationContentParser(SchemaDefinition::createFromSchemaString($schema));
        $output = $schemaParser->parse();

        $expectedMigrationContent = "
        \$table->string('name');
        \$table->integer('age');
        \$table->decimal('price');
        \$table->enum('status', ['approved', 'rejected']);
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedMigrationContent),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    /**
     * @test
     */
    public function ParseGeneratesResourceAttributes(): void
    {
        $schema = 'name:string|age:integer|price:decimal|opened_at:datetime|status:enum(approved,rejected)';
        $schemaParser = new ResourceAttributesParser(SchemaDefinition::createFromSchemaString($schema));
        $output = $schemaParser->parse();

        $expectedResourceContent = "
        'name' => \$this->name,
        'age' => \$this->age,
        'price' => \$this->price,
        'opened_at' => dateTimeFormat(\$this->opened_at),
        'status' => \$this->status,
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedResourceContent),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    /**
     * @test
     */
    public function ParseGeneratesCreateValidationRules(): void
    {
        $schema = 'name:string:nullable|age:integer|price:decimal|status:enum(approved,rejected)';
        $schemaParser = new CreateValidationRulesParser(SchemaDefinition::createFromSchemaString($schema));
        $output = $schemaParser->parse();

        $expectedValidationRulesForCreate = "
        'name' => ['nullable', 'string'],
        'age' => ['required', 'integer'],
        'price' => ['required', 'numeric'],
        'status' => ['required', 'in:approved,rejected'],
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedValidationRulesForCreate),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    /**
     * @test
     */
    public function ParseGeneratesUpdateValidationRules(): void
    {
        $schema = 'email:string|age:integer|price:decimal';
        $schemaParser = new UpdateValidationRulesParser(SchemaDefinition::createFromSchemaString($schema));
        $output = $schemaParser->parse();

        $expectedValidationRulesForUpdate = "
        'email' => ['sometimes', 'email', 'string'],
        'age' => ['sometimes', 'integer'],
        'price' => ['sometimes', 'numeric'],
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedValidationRulesForUpdate),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    /**
     * @test
     */
    public function ParseGeneratesForeignKeyWithCascadeOption(): void
    {
        $schema = 'author_id:foreignId:cascadeOnDelete';
        $schemaParser = new MigrationContentParser(SchemaDefinition::createFromSchemaString($schema));
        $output = $schemaParser->parse();

        $expectedMigrationContent = "
        \$table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedMigrationContent),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    /**
     * @dataProvider schemasProvider
     * @test
     */
    public function ValidateSchema($schema, $isValid): void
    {
        if ($isValid) {
            $schemaDefinition = SchemaDefinition::createFromSchemaString($schema);
            $this->assertInstanceOf(SchemaDefinition::class, $schemaDefinition);
            $this->assertCount(count(explode('|', $schema)), $schemaDefinition->getColumns());
        } else {
            $this->expectException(SchemaNotValidException::class);
            SchemaDefinition::createFromSchemaString($schema);
        }
    }
}
