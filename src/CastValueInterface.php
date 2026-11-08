<?php

declare(strict_types=1);

namespace UIAwesome\Model;

/**
 * Contract for custom value casters used by `#[Cast]`.
 */
interface CastValueInterface
{
    /**
     * Casts the incoming value into a target representation.
     */
    public function cast(mixed $value): mixed;
}
