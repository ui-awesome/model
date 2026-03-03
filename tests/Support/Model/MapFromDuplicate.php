<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\MapFrom;
use UIAwesome\Model\BaseModel;

/**
 * Stub model with duplicate MapFrom keys used for tests.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class MapFromDuplicate extends BaseModel
{
    #[MapFrom('duplicate-key')]
    public string $first = '';

    #[MapFrom('duplicate-key')]
    public string $second = '';
}
