<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class DoNotCollect {}
