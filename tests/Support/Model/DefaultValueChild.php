<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

/**
 * Stub child model reusing a parent property name for tests.
 */
final class DefaultValueChild extends DefaultValueParent
{
    public string|null $status = '';
}
