<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;
use InvalidArgumentException;
use UIAwesome\Model\Exception\Message;

use function trim;

/**
 * Forces custom casting before native property type casting.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Cast
{
    /**
     * Separates string items when casting to an array.
     */
    public string $separator;
    /**
     * Identifies the cast type or custom caster class.
     */
    public string $target;

    /**
     * Configures a nonempty cast target and array separator.
     *
     * @param string $target Cast type or custom caster class.
     * @param string $separator Nonempty separator for array casts.
     *
     * @throws InvalidArgumentException When the target or separator is empty.
     */
    public function __construct(string $target, string $separator = ',')
    {
        if (trim($target) === '') {
            throw new InvalidArgumentException(Message::CAST_TARGET_EMPTY->getMessage());
        }

        if ($separator === '') {
            throw new InvalidArgumentException(Message::CAST_SEPARATOR_EMPTY->getMessage());
        }

        $this->target = $target;
        $this->separator = $separator;
    }
}
