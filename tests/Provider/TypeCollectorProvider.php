<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Provider;

/**
 * Data provider for {@see \UIAwesome\Model\Tests\TypeCollectorTest} test cases.
 *
 * Provides representative input/output pairs for property type checks and property assignment scenarios.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class TypeCollectorProvider
{
    /**
     * @phpstan-return array<string, array{string, string, bool}>
     */
    public static function isPropertyTypeChecks(): array
    {
        return [
            'array property accepts array' => ['array', 'array', true],
            'bool property accepts bool' => ['bool', 'bool', true],
            'float property accepts float' => ['float', 'float', true],
            'int property accepts int' => ['int', 'int', true],
            'nullable property accepts int' => ['nullable', 'int', true],
            'nullable property accepts null' => ['nullable', 'null', true],
            'object property accepts null' => ['object', 'null', true],
            'object property accepts object' => ['object', 'object', true],
            'string property accepts string' => ['string', 'string', true],
            'withoutType property accepts empty type' => ['withoutType', '', true],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, mixed, mixed}>
     */
    public static function setPropertyValueCases(): array
    {
        return [
            'array value' => ['array', [], []],
            'string value' => ['string', 'string', 'string'],
            'integer value' => ['int', 1, 1],
            'boolean value' => ['bool', true, true],
            'nullable object value as null' => ['object', null, null],
            'nullable int value as null' => ['nullable', null, null],
            'nullable int value as integer' => ['nullable', 1, 1],
            'nullable int value as numeric string' => ['nullable', '2', 2],
            'without type accepts any value' => ['withoutType', 1, 1],
        ];
    }
}
