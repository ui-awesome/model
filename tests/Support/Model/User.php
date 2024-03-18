<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;

final class User extends AbstractModel
{
    public string $name;

    public function __construct(public Profile $profile) {}
}
