<?php

namespace Essa\APIToolKit\Generator\Contracts;

interface PathHasClass
{
    public function getNameSpace(): string;

    public function getClassName(): string;
}
