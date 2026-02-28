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
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Timestamp {}
