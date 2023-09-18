<?php

namespace Essa\APIToolKit\Generator\Contracts;

interface ClassInfoInterface
{
    public function getNameSpace(): string;

    public function getClassName(): string;
}
