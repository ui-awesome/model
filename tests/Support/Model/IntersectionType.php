<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;
use UIAwesome\Model\Tests\Support\Contract\{IntersectionLeft, IntersectionRight};

/**
 * Stub model with intersection-typed property used for tests.
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class IntersectionType extends BaseModel
{
    private IntersectionLeft&IntersectionRight $intersection;
}
