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
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class NoSnakeCase {}
