<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Generator\DTOs\SchemaParserOutput;
use Essa\APIToolKit\Generator\SchemaParser;

class SchemaParserTest extends TestCase
{
    public function testParseWithEmptySchemaReturnsEmptyOutput(): void
    {
        $schemaParser = new SchemaParser(null);
        $output = $schemaParser->parse();

        $this->assertInstanceOf(SchemaParserOutput::class, $output);
        $this->assertEmpty($output->migrationContent);
    }

    public function testParseGeneratesMigrationContent(): void
    {
        $schema = 'name:string,age:integer';
        $schemaParser = new SchemaParser($schema);
        $output = $schemaParser->parse();

        $expectedMigrationContent = "
            \$table->string('name');
            \$table->integer('age');
        ";

        $this->assertInstanceOf(SchemaParserOutput::class, $output);
        $this->assertEquals(
            $this->normalizeWhitespaceAndNewlines($expectedMigrationContent),
            $this->normalizeWhitespaceAndNewlines($output->migrationContent)
        );
    }

    public function testGenerateRelationshipMethod(): void
    {
        $schema = 'author_id:foreignId,category_id:foreignId';
        $schemaParser = new SchemaParser($schema);
        $output = $schemaParser->parse();

        $relationshipMethod = $output->modelRelations;

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
            $this->normalizeWhitespaceAndNewlines($relationshipMethod)
        );
    }

    public function testParseGeneratesFactoryColumns(): void
    {
        $schema = 'name:string,age:integer,price:decimal';
        $schemaParser = new SchemaParser($schema);
        $output = $schemaParser->parse();

        $expectedFactoryContent = "
        'name' => \$this->faker->firstName,
        'age' => \$this->faker->randomNumber,
        'price' => \$this->faker->randomFloat,
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedFactoryContent),
            $this->normalizeWhitespaceAndNewlines($output->factoryContent)
        );
    }

    public function testParseGeneratesColumnDefinitions(): void
    {
        $schema = 'name:string,age:integer,price:decimal';
        $schemaParser = new SchemaParser($schema);
        $output = $schemaParser->parse();

        $expectedMigrationContent = "
        \$table->string('name');
        \$table->integer('age');
        \$table->decimal('price');
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedMigrationContent),
            $this->normalizeWhitespaceAndNewlines($output->migrationContent)
        );
    }

    public function testParseGeneratesResourceAttributes(): void
    {
        $schema = 'name:string,age:integer,price:decimal';
        $schemaParser = new SchemaParser($schema);
        $output = $schemaParser->parse();

        $expectedResourceContent = "
        'name' => \$this->name,
        'age' => \$this->age,
        'price' => \$this->price,
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedResourceContent),
            $this->normalizeWhitespaceAndNewlines($output->resourceContent)
        );
    }

    public function testParseGeneratesValidationRules(): void
    {
        $schema = 'name:string,age:integer,price:decimal';
        $schemaParser = new SchemaParser($schema);
        $output = $schemaParser->parse();

        $expectedValidationRulesForCreate = "
        'name' => 'required',
        'age' => 'required',
        'price' => 'required',
    ";


        $expectedValidationRulesForUpdate = "
        'name' => 'sometimes',
        'age' => 'sometimes',
        'price' => 'sometimes',
    ";

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedValidationRulesForCreate),
            $this->normalizeWhitespaceAndNewlines($output->createValidationRules)
        );

        $this->assertStringContainsString(
            $this->normalizeWhitespaceAndNewlines($expectedValidationRulesForUpdate),
            $this->normalizeWhitespaceAndNewlines($output->updateValidationRules)
        );
    }

    public function testParseGeneratesForeignKeyWithCascadeOption(): void
    {
        $schema = 'author_id:foreignId:cascadeOnDelete';
        $schemaParser = new SchemaParser($schema);
        $output = $schemaParser->parse();

        $expectedMigrationContent = "
        \$table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
    ";

        $this->assertStringContainsString($this->normalizeWhitespaceAndNewlines($expectedMigrationContent), $this->normalizeWhitespaceAndNewlines($output->migrationContent));
    }

    private function normalizeWhitespaceAndNewlines(string $content): string
    {
        $content = preg_replace('/\s+/', ' ', $content);
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        return trim($content);
    }
}
