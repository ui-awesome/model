<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;

final class DynamicNested extends AbstractModel
{
    public function __construct(private readonly Dynamic $dynamic) {}
}
