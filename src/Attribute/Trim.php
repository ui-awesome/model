<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;

/**
 * Trims leading and trailing whitespace from string input before assignment.
 *
 * Usage example:
 * ```php
 * #[Trim]
 * public string $name = '';
 * ```
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Trim {}
