<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;

/**
 * Applies a runtime default when assigned input is `null` or an empty string.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class DefaultValue
{
    /**
     * Configures the value applied to `null` or empty-string input.
     *
     * @param mixed $value Runtime default value.
     */
    public function __construct(public mixed $value) {}
}
