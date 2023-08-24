<?php

namespace Essa\APIToolKit\Generator\DTOs;

class GenerationConfiguration
{
    public function __construct(private string $model, private array $userChoices, private ?string $schema)
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

    public function getSchema(): ?string
    {
        return $this->schema;
    }
}
