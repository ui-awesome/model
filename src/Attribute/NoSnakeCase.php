<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;

/**
 * Prevents key conversion to snake_case during array serialization.
 *
 * Usage example:
 * ```php
 * #[NoSnakeCase]
 * public string $apiVersion = '';
 * ```
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class NoSnakeCase {}
