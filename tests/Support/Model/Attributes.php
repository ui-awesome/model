<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\DoNotCollect;
use UIAwesome\Model\Attribute\Timestamp;

/**
 * Stub model with attribute-driven properties used for tests.
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class Attributes extends AbstractModel
{
    #[Timestamp]
    private int $createdAt = 0;

    #[DoNotCollect]
    private string $flag = '';

    private string $name = '';

    #[Timestamp]
    private int $updatedAt = 0;
}
