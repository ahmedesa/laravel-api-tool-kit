<?php

namespace Essa\APIToolKit\Generator\Guessers;

use Essa\APIToolKit\Generator\ColumnDefinition;
use Essa\APIToolKit\Generator\Contracts\GuesserInterface;

class ValidationRuleGuesserInterface implements GuesserInterface
{
    public function __construct(private ColumnDefinition $definition, private array $extraValidation)
    {
    }

    public function guess(): string
    {
        $rules = $this->addExtraValidationToTheRules();

        if ($this->definition->isEnumType()) {
            $rules[] = 'in:' . implode(',', $this->definition->getEnumValues());
        }

        if ($this->definition->isEmailType()) {
            $rules[] = 'email';
        }

        if ($this->definition->isImageType()) {
            $rules[] = 'image';
        }

        if ($this->definition->isStringOrTextType()) {
            $rules[] = 'string';
        }

        if ($this->definition->isIntegerType()) {
            $rules[] = 'integer';
        }

        if ($this->definition->isBooleanType()) {
            $rules[] = 'boolean';
        }

        if ($this->definition->isNumericType()) {
            $rules[] = 'numeric';
        }

        if ($this->definition->isDateType()) {
            $rules[] = 'date';
        }

        if ($this->definition->isTimeType()) {
            $rules[] = 'date_format:H:i:s';
        }

        if ($this->definition->isUuidType()) {
            $rules[] = 'uuid';
        }

        if ($this->definition->isIpAddressType()) {
            $rules[] = 'ip';
        }

        if ($this->definition->isJsonType()) {
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
