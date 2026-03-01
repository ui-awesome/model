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
     * Indicates duplicate input-key mapping across model properties.
     *
     * Format: "Duplicate MapFrom key '%s' in '%s::%s' and '%s::%s'."
     */
    case DUPLICATE_MAP_FROM_KEY = "Duplicate MapFrom key '%s' in '%s::%s' and '%s::%s'.";
    /**
     * Indicates invalid string input for date/time object casting.
     *
     * Format: "Invalid date/time string '%s' for type '%s'."
     */
    case INVALID_DATE_TIME_STRING = "Invalid date/time string '%s' for type '%s'.";

    /**
     * Indicates an empty MapFrom input key.
     *
     * Format: "MapFrom key cannot be empty."
     */
    case MAP_FROM_KEY_EMPTY = 'MapFrom key cannot be empty.';

    /**
     * Indicates an empty Cast target.
     *
     * Format: "Cast target cannot be empty."
     */
    case CAST_TARGET_EMPTY = 'Cast target cannot be empty.';

    /**
     * Indicates an empty Cast separator.
     *
     * Format: "Cast separator cannot be empty."
     */
    case CAST_SEPARATOR_EMPTY = 'Cast separator cannot be empty.';

    /**
     * Indicates unsupported cast target for a model property.
     *
     * Format: "Invalid Cast target '%s' for '%s::%s'."
     */
    case INVALID_CAST_TARGET = "Invalid Cast target '%s' for '%s::%s'.";

    /**
     * Indicates cast class does not implement required caster contract.
     *
     * Format: "Cast target '%s' for '%s::%s' must implement '%s'."
     */
    case INVALID_CAST_CLASS = "Cast target '%s' for '%s::%s' must implement '%s'.";

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
