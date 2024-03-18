<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;

final class DinamicNested extends AbstractModel
{
    public function __construct(private Dinamic $dinamic) {}
}
