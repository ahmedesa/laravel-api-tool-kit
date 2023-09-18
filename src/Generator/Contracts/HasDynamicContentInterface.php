<?php

namespace Essa\APIToolKit\Generator\Contracts;

interface HasDynamicContentInterface
{
    public function getContent(): array;
}
