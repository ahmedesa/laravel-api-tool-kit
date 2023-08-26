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
        $rules = $this->extraValidation + [];

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
}
