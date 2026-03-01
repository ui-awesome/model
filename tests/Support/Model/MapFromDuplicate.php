<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\MapFrom;

/**
 * Stub model with duplicate MapFrom keys used by tests.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class MapFromDuplicate extends AbstractModel
{
    #[MapFrom('duplicate-key')]
    public string $first = '';
    #[MapFrom('duplicate-key')]
    public string $second = '';
}
