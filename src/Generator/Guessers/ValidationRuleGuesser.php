<?php

namespace Essa\APIToolKit\Generator\Guessers;

use Essa\APIToolKit\Generator\Contracts\Guesser;
use Essa\APIToolKit\Generator\DTOs\ColumnDefinition;

class ValidationRuleGuesser implements Guesser
{
    public function __construct(private ColumnDefinition $definition, private array $extraValidation)
    {
    }

    public function guess(): string
    {
        $rules = $this->addExtraValidationToTheRules();

        if (str_contains($this->definition->getName(), 'email')) {

            $rules[] = 'email';
        }

        if (str_contains($this->definition->getName(), 'image')) {
            $rules[] = 'image';
        }

        if (in_array($this->definition->getType(), ['string', 'text'])) {
            $rules[] = 'string';
        }

        if (in_array($this->definition->getType(), ['integer', 'bigInteger', 'unsignedBigInteger', 'mediumInteger', 'tinyInteger', 'smallInteger'])) {
            $rules[] = 'integer';
        }

        if ('boolean' === $this->definition->getType()) {
            $rules[] = 'boolean';
        }

        if (in_array($this->definition->getType(), ['decimal', 'double', 'float'])) {
            $rules[] = 'numeric';
        }

        if (in_array($this->definition->getType(), ['date', 'dateTime', 'dateTimeTz', 'timestamp', 'timestampTz'])) {
            $rules[] = 'date';
        }

        if (in_array($this->definition->getType(), ['time', 'timeTz'])) {
            $rules[] = 'date_format:H:i:s';
        }

        if (in_array($this->definition->getType(), ['uuid', 'uuidBinary'])) {
            $rules[] = 'uuid';
        }

        if ('ipAddress' === $this->definition->getType()) {
            $rules[] = 'ip';
        }

        if (in_array($this->definition->getType(), ['json', 'jsonb'])) {
            $rules[] = 'json';
        }

        return "'" . implode("', '", $rules) . "'";
    }

    private function shouldAddNullableRule(): bool
    {
        return in_array('nullable', $this->definition->getOptions()) && $this->extraValidation === ['required'];
    }

    private function addExtraValidationToTheRules(): array
    {
        if ( ! $this->shouldAddNullableRule()) {
            return $this->extraValidation;
        }

        return ['nullable'];
    }
}
