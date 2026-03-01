<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

/**
 * Stub child model reusing a parent property name for tests.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class DefaultValueChild extends DefaultValueParent
{
    public string|null $status = '';
}
