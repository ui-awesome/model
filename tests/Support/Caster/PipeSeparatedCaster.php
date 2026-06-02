<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Caster;

use UIAwesome\Model\CastValueInterface;

use function array_filter;
use function array_map;
use function array_values;
use function explode;
use function is_string;
use function trim;

/**
 * Stub caster that converts pipe-separated strings to trimmed arrays for tests.
 */
final class PipeSeparatedCaster implements CastValueInterface
{
    public function cast(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $items = array_map(
            trim(...),
            explode('|', $value),
        );

        return array_values(
            array_filter(
                $items,
                static fn(string $item): bool => $item !== '',
            ),
        );
    }
}
