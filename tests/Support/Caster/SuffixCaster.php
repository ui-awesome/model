<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Caster;

use UIAwesome\Model\CastValueInterface;

use function is_string;

/**
 * Non-idempotent cast used to detect repeated transformations during cloning.
 */
final class SuffixCaster implements CastValueInterface
{
    public function cast(mixed $value): mixed
    {
        return is_string($value) ? "{$value}-cast" : $value;
    }
}
