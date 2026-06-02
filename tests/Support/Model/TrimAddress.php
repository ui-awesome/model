<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\Trim;
use UIAwesome\Model\BaseModel;

/**
 * Stub nested model with trim-enabled properties for tests.
 */
final class TrimAddress extends BaseModel
{
    #[Trim]
    public string $city = '';
}
