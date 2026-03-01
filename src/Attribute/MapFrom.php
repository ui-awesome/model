<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;
use InvalidArgumentException;
use UIAwesome\Model\Exception\Message;

use function trim;

/**
 * Maps a model property from an explicit input key.
 *
 * Usage example:
 * ```php
 * #[MapFrom('user-email-address')]
 * public string $publicEmail = '';
 * ```
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class MapFrom
{
    public readonly string $key;

    public function __construct(string $key)
    {
        if (trim($key) === '') {
            throw new InvalidArgumentException(
                Message::MAP_FROM_KEY_EMPTY->getMessage(),
            );
        }

        $this->key = $key;
    }
}
