<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;

/**
 * Marks a property as a timestamp field initialized on assignment.
 *
 * Usage example:
 * ```php
 * #[Timestamp]
 * public int $createdAt = 0;
 * ```
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Timestamp {}
