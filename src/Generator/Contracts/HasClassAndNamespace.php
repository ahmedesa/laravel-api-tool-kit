<?php

namespace Essa\APIToolKit\Generator\Contracts;

interface HasClassAndNamespace
{
    public function getNameSpace(): string;

    public function getClassName(): string;
}
