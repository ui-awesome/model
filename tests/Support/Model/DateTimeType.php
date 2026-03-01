<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use DateTime;
use DateTimeImmutable;
use UIAwesome\Model\AbstractModel;

/**
 * Stub model declaring DateTime typed properties for tests.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class DateTimeType extends AbstractModel
{
    public DateTime $createdAt;
    public DateTimeImmutable|null $publishedAt = null;
    public DateTimeImmutable $updatedAt;
}
