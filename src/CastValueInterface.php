<?php

declare(strict_types=1);

namespace UIAwesome\Model;

/**
 * Contract for custom value casters used by `#[Cast]`.
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
     * $model->cast('1,2,3');
     * ```
     */
    public function cast(mixed $value): mixed;
}
