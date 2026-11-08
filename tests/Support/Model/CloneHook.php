<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;

/**
 * Model whose clone hook does not invoke a parent hook.
 */
final class CloneHook extends BaseModel
{
    public string $name = '';

    public function __clone(): void {}
}
