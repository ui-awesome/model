<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use DateTime;
use DateTimeImmutable;
use UIAwesome\Model\BaseModel;

/**
 * Stub model declaring DateTime typed properties for tests.
 */
final class DateTimeType extends BaseModel
{
    public DateTime $createdAt;

    public DateTimeImmutable|null $publishedAt = null;

    public DateTimeImmutable $updatedAt;
}
