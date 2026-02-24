<?php

namespace Essa\APIToolKit\Tests\Mocks;

use Essa\APIToolKit\Enum\EnumHelpers;

enum TestBackedEnum: string
{
    use EnumHelpers;

    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';
}
