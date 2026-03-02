<?php

declare(strict_types=1);

namespace UIAwesome\Model;

/**
 * Contract for custom value casters used by `#[Cast]`.
 *
 * Usage example:
 * ```php
 * final class PipeSeparatedCaster implements CastValueInterface
 * {
 *     public function cast(mixed $value): mixed
 *     {
 *         if (!is_string($value)) {
 *             throw new \InvalidArgumentException('Value must be a string.');
 *         }
 *
 *         return \explode('|', $value);
 *     }
 * }
 * ```
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
interface CastValueInterface
{
    /**
     * Casts the incoming value into a target representation.
     *
     * Usage example:
     * ```php
     * $caster = new PipeSeparatedCaster();
     * $result = $caster->cast('a|b|c');
     * ```
     */
    public function cast(mixed $value): mixed;
}
