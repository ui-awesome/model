<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;

/**
 * Stub model with union-typed property used for tests.
 */
final class UnionType extends BaseModel
{
    private bool|int|object|string|null $union = null;
}
