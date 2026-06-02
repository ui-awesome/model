<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;

/**
 * Applies a runtime default when assigned input is `null` or an empty string.
 *
 * Usage example:
 * ```php
 * #[DefaultValue('Guest')]
 * public string $displayName = '';
 * ```
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class DefaultValue
{
    public function __construct(public mixed $value) {}
}
