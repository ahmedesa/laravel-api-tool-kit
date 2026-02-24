<?php

declare(strict_types=1);

namespace Essa\APIToolKit\Enum;

enum CacheKeys: string
{
    use EnumHelpers;

    case DEFAULT_CACHE_KEY = 'default.all';
}
