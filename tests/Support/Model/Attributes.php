<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\DoNotCollect;
use UIAwesome\Model\Attribute\Timestamp;

final class Attributes extends AbstractModel
{
    private string $name = '';
    #[Timestamp]
    private int $createdAt = 0;
    #[DoNotCollect]
    private string $flag = '';
    #[Timestamp]
    private int $updatedAt = 0;
}
