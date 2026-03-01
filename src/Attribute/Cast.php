<?php

declare(strict_types=1);

namespace UIAwesome\Model\Attribute;

use Attribute;
use InvalidArgumentException;
use UIAwesome\Model\Exception\Message;

use function trim;

/**
 * Forces custom casting before native property type casting.
 *
 * Usage examples:
 * ```php
 * #[Cast('array')]
 * public array $tags = [];
 * #[Cast(App\Model\CsvToArrayCaster::class)]
 * public array $keywords = [];
 * ```
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Cast
{
    public readonly string $separator;
    public readonly string $target;

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
