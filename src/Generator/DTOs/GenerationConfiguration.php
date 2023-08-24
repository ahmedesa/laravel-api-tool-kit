<?php

namespace Essa\APIToolKit\Generator\DTOs;

class GenerationConfiguration
{
    public function __construct(private string $model, private array $userChoices, private SchemaDefinition $schema)
    {
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getUserChoices(): array
    {
        return $this->userChoices;
    }

    public function getSchema(): SchemaDefinition
    {
        return $this->schema;
    }
}
