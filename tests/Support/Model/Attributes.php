<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\DoNotCollect;
use UIAwesome\Model\Attribute\Timestamp;
use UIAwesome\Model\BaseModel;

/**
 * Stub model with attribute-driven properties used for tests.
 */
final class Attributes extends BaseModel
{
    #[Timestamp]
    private int $createdAt = 0;

    #[DoNotCollect]
    private string $flag = '';

    private string $name = '';

    #[Timestamp]
    private int $updatedAt = 0;
}
