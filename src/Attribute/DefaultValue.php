<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;

/**
 * Applies a runtime default when assigned input is null or an empty string.
 *
 * Usage example:
 * ```php
 * #[DefaultValue('Guest')]
 * public string $displayName = '';
 * ```
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class DefaultValue
{
    public function __construct(public readonly mixed $value) {}
}
