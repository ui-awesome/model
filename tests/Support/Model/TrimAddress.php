<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\Trim;
use UIAwesome\Model\BaseModel;

/**
 * Stub nested model with trim-enabled properties for tests.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class TrimAddress extends BaseModel
{
    #[Trim]
    public string $city = '';
}
