<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;
use InvalidArgumentException;
use UIAwesome\Model\Exception\Message;

/**
 * Maps a model property from an explicit input key.
 *
 * Usage example:
 * ```php
 * #[MapFrom('user-email-address')]
 * public string $publicEmail = '';
 * ```
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class MapFrom
{
    public readonly string $key;

    public function __construct(string $key)
    {
        if ($key === '') {
            throw new InvalidArgumentException(
                Message::MAP_FROM_KEY_EMPTY->getMessage(),
            );
        }

        $this->key = $key;
    }
}
