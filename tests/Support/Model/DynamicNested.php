<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;

/**
 * Stub nested dynamic model used for tests.
 */
final class DynamicNested extends BaseModel
{
    public function __construct(private readonly Dynamic $dynamic) {}
}
