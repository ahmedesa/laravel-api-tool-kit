<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Generator\SchemaParsers\CreateValidationRulesParser;
use Essa\APIToolKit\Generator\SchemaParsers\FactoryColumnsParser;
use Essa\APIToolKit\Generator\SchemaParsers\FillableColumnsParser;
use Essa\APIToolKit\Generator\SchemaParsers\MigrationContentParser;
use Essa\APIToolKit\Generator\SchemaParsers\RelationshipMethodsParser;
use Essa\APIToolKit\Generator\SchemaParsers\ResourceAttributesParser;
use Essa\APIToolKit\Generator\SchemaParsers\UpdateValidationRulesParser;

class SchemaParserTest extends TestCase
{
    //    public function testParseWithEmptySchemaReturnsEmptyOutput(): void
    //    {
    //        $schemaParser = new SchemaParser(null);
    //        $output = $schemaParser->parse($this->getSchema($schema));
    //
    //        $this->assertInstanceOf(SchemaParserOutput::class, $output);
    //        $this->assertEmpty($output->migrationContent);
    //    }

    /**
     * @test
     */
    public function testGenerateFillableColumns(): void
    {
        $schema = 'name:string,age:integer,email:string:unique';
        $schemaParser = new FillableColumnsParser();
        $output = $schemaParser->parse($this->getSchema($schema));

        $expectedFillableColumns = "
            'name',
            'age',
            'email',
        ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedFillableColumns),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    public function testParseGeneratesMigrationContent(): void
    {
        $schema = 'name:string,age:integer';
        $schemaParser = new MigrationContentParser();
        $output = $schemaParser->parse($this->getSchema($schema));

        $expectedMigrationContent = "
            \$table->string('name');
            \$table->integer('age');
        ";

        $this->assertEquals(
            $this->normalizeWhitespaceAndNewlines($expectedMigrationContent),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    public function testGenerateRelationshipMethod(): void
    {
        $schema = 'author_id:foreignId,category_id:foreignId';
        $schemaParser = new RelationshipMethodsParser($schema);
        $output = $schemaParser->parse($this->getSchema($schema));

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

    public function testParseGeneratesFactoryColumns(): void
    {
        $schema = 'name:string,age:integer,price:decimal';
        $schemaParser = new FactoryColumnsParser();
        $output = $schemaParser->parse($this->getSchema($schema));

        $expectedFactoryContent = "
        'name' => \$this->faker->firstName,
        'age' => \$this->faker->randomNumber,
        'price' => \$this->faker->randomFloat,
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedFactoryContent),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    public function testParseGeneratesColumnDefinitions(): void
    {
        $schema = 'name:string,age:integer,price:decimal';
        $schemaParser = new MigrationContentParser();
        $output = $schemaParser->parse($this->getSchema($schema));

        $expectedMigrationContent = "
        \$table->string('name');
        \$table->integer('age');
        \$table->decimal('price');
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedMigrationContent),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    public function testParseGeneratesResourceAttributes(): void
    {
        $schema = 'name:string,age:integer,price:decimal';
        $schemaParser = new ResourceAttributesParser();
        $output = $schemaParser->parse($this->getSchema($schema));

        $expectedResourceContent = "
        'name' => \$this->name,
        'age' => \$this->age,
        'price' => \$this->price,
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedResourceContent),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    public function testParseGeneratesCreateValidationRules(): void
    {
        $schema = 'name:string,age:integer,price:decimal';
        $schemaParser = new CreateValidationRulesParser();
        $output = $schemaParser->parse($this->getSchema($schema));

        $expectedValidationRulesForCreate = "
        'name' => 'required',
        'age' => 'required',
        'price' => 'required',
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedValidationRulesForCreate),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    public function testParseGeneratesUpdateValidationRules(): void
    {
        $schema = 'name:string,age:integer,price:decimal';
        $schemaParser = new UpdateValidationRulesParser();
        $output = $schemaParser->parse($this->getSchema($schema));

        $expectedValidationRulesForUpdate = "
        'name' => 'sometimes',
        'age' => 'sometimes',
        'price' => 'sometimes',
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedValidationRulesForUpdate),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    public function testParseGeneratesForeignKeyWithCascadeOption(): void
    {
        $schema = 'author_id:foreignId:cascadeOnDelete';
        $schemaParser = new MigrationContentParser();
        $output = $schemaParser->parse($this->getSchema($schema));

        $expectedMigrationContent = "
        \$table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedMigrationContent),
            $this->normalizeWhitespaceAndNewlines($output)
        );
    }

    private function getSchema($schema): ?array
    {
        if ( ! $schema) {
            return null;
        }

        return explode(',', $schema);
    }
}