<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;

final class UnionType extends AbstractModel
{
    private bool|int|object|string|null $union = null;
}
