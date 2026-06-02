<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;

/**
 * Stub container model for nested trim tests.
 */
final class TrimContainer extends BaseModel
{
    public function __construct(public readonly TrimAddress $address) {}
}
