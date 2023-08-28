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

        if ($this->definition->isEnum()) {
            $rules[] = 'in:' . implode(',', $this->definition->getEnumValues());
        }

        if ($this->isEmailColumn()) {
            $rules[] = 'email';
        }

        if ($this->isImageColumn()) {
            $rules[] = 'image';
        }

        if ($this->isStringOrText()) {
            $rules[] = 'string';
        }

        if ($this->isIntegerType()) {
            $rules[] = 'integer';
        }

        if ($this->isBooleanType()) {
            $rules[] = 'boolean';
        }

        if ($this->isNumericType()) {
            $rules[] = 'numeric';
        }

        if ($this->isDateType()) {
            $rules[] = 'date';
        }

        if ($this->isTimeType()) {
            $rules[] = 'date_format:H:i:s';
        }

        if ($this->isUuidType()) {
            $rules[] = 'uuid';
        }

        if ($this->isIpAddressType()) {
            $rules[] = 'ip';
        }

        if ($this->isJsonType()) {
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

    private function isEmailColumn(): bool
    {
        return str_contains($this->definition->getName(), 'email');
    }

    private function isImageColumn(): bool
    {
        return str_contains($this->definition->getName(), 'image');
    }

    private function isStringOrText(): bool
    {
        return in_array($this->definition->getType(), ['string', 'text']) && ! $this->isImageColumn();
    }

    private function isIntegerType(): bool
    {
        return in_array($this->definition->getType(), ['integer', 'bigInteger', 'unsignedBigInteger', 'mediumInteger', 'tinyInteger', 'smallInteger']);
    }

    private function isBooleanType(): bool
    {
        return 'boolean' === $this->definition->getType();
    }

    private function isNumericType(): bool
    {
        return in_array($this->definition->getType(), ['decimal', 'double', 'float']);
    }

    private function isDateType(): bool
    {
        return in_array($this->definition->getType(), ['date', 'dateTime', 'dateTimeTz', 'timestamp', 'timestampTz']);
    }

    private function isTimeType(): bool
    {
        return in_array($this->definition->getType(), ['time', 'timeTz']);
    }

    private function isUuidType(): bool
    {
        return in_array($this->definition->getType(), ['uuid', 'uuidBinary']);
    }

    private function isIpAddressType(): bool
    {
        return 'ipAddress' === $this->definition->getType();
    }

    private function isJsonType(): bool
    {
        return in_array($this->definition->getType(), ['json', 'jsonb']);
    }
}
