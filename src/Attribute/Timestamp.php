<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;

/**
 * Marks a property as a timestamp field initialized on assignment.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Timestamp {}
