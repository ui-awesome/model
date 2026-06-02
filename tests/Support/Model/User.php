<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;

/**
 * Stub user model with nested profile relation used for tests.
 */
final class User extends BaseModel
{
    public string $name;

    public function __construct(public Profile $profile) {}
}
