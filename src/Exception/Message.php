<?php

declare(strict_types=1);

namespace UIAwesome\Model\Exception;

use function sprintf;

/**
 * Represents reusable error message templates for model exceptions.
 *
 * Use {@see Message::getMessage()} to format template values via `sprintf()` placeholders.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
enum Message: string
{
    /**
     * Indicates attempts to overwrite an initialized readonly property.
     *
     * Format: "Cannot overwrite initialized readonly property: '%s::%s'."
     */
    case READONLY_PROPERTY_ALREADY_INITIALIZED = "Cannot overwrite initialized readonly property: '%s::%s'.";

    /**
     * Indicates an undefined property without model class context.
     *
     * Format: "Undefined property: '%s'."
     */
    case UNDEFINED_PROPERTY = "Undefined property: '%s'.";

    /**
     * Indicates an undefined property including model class context.
     *
     * Format: "Undefined property: '%s::%s'."
     */
    case UNDEFINED_PROPERTY_WITH_CLASS = "Undefined property: '%s::%s'.";

    /**
     * Returns the formatted message string for the error case.
     *
     * Usage example:
     * ```php
     * throw new InvalidArgumentException(
     *     \UIAwesome\Model\Exception\Message::UNDEFINED_PROPERTY->getMessage('name')
     * );
     * ```
     *
     * @param int|string ...$argument Values to insert into the message template.
     *
     * @return string Formatted error message with interpolated arguments.
     */
    public function getMessage(int|string ...$argument): string
    {
        return sprintf($this->value, ...$argument);
    }
}
