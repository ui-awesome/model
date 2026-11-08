<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;

/**
 * Excludes a property from automatic type collection.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class DoNotCollect {}
