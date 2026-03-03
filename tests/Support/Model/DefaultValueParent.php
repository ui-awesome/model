<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\{DefaultValue, DoNotCollect};
use UIAwesome\Model\BaseModel;

/**
 * Stub parent model combining `DoNotCollect` and `DefaultValue` metadata for tests.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
class DefaultValueParent extends BaseModel
{
    #[DoNotCollect]
    #[DefaultValue('parent-default')]
    private string $status = '';
}
