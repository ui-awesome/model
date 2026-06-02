<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\MapFrom;
use UIAwesome\Model\BaseModel;

/**
 * Stub model with duplicate MapFrom keys used for tests.
 */
final class MapFromDuplicate extends BaseModel
{
    #[MapFrom('duplicate-key')]
    public string $first = '';

    #[MapFrom('duplicate-key')]
    public string $second = '';
}
