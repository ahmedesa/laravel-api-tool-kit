<?php

namespace Essa\APIToolKit\Generator\Contracts;

interface HasDynamicContent
{
    public function getContent(): array;
}
