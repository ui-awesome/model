<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;
use InvalidArgumentException;
use UIAwesome\Model\Exception\Message;

use function trim;

/**
 * Maps a model property from an explicit input key.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class MapFrom
{
    /**
     * Identifies the external input key mapped to this property.
     */
    public string $key;

    /**
     * Configures the external input key.
     *
     * @param string $key Nonempty input key.
     *
     * @throws InvalidArgumentException When the key is empty.
     */
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
