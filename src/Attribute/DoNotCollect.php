<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;

/**
 * Excludes a property from automatic type collection.
 *
 * Usage example:
 * ```php
 * #[DoNotCollect]
 * public string $internalToken = '';
 * ```
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class DoNotCollect {}
