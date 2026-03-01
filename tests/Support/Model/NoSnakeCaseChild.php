<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

/**
 * Child stub model reusing a parent property name for tests.
 */
final class NoSnakeCaseChild extends NoSnakeCaseParent
{
    public string $apiVersion = '';
}
