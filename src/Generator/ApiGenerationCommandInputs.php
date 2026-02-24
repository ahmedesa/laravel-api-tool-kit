<?php

declare(strict_types=1);

namespace Essa\APIToolKit\Generator;

class ApiGenerationCommandInputs
{
    public function __construct(
        private string $model,
        private array $userChoices,
        private SchemaDefinition $schema,
        private string $pathGroup
    ) {
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

    public function isOptionSelected(string $option): bool
    {
        return $this->userChoices[$option];
    }

    public function getPathGroup(): string
    {
        return $this->pathGroup;
    }

    public function setPathGroup(string $pathGroup): void
    {
        $this->pathGroup = $pathGroup;
    }
}
