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
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class DoNotCollect {}
