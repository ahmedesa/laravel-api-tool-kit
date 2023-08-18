<?php

namespace Essa\APIToolKit\Traits;

trait IncludeTranslation
{
    public function validatedWithTranslation(): array
    {
        $request = $this->validated();

        if ( ! $this->has('translations')) {
            return $request;
        }

        $name_translations = $request['translations'];

        unset($request['translations']);

        return array_merge($request, $name_translations);
    }
}
