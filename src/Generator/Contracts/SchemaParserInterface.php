<?php

namespace Essa\APIToolKit\Generator\Contracts;

interface SchemaParserInterface
{
    public function parse(array $columnDefinitions): string;
}
