<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Provider;

use stdClass;

/**
 * Data provider for {@see \UIAwesome\Model\Tests\ModelTest} test cases.
 *
 * Provides representative input/output pairs for typed property assignment and bulk property setting.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class ModelProvider
{
    /**
     * @phpstan-return array<string, array{string, mixed, mixed, string}>
     */
    public static function propertyValueAssignments(): array
    {
        $object = new stdClass();

        return [
            'array value' => ['array', [1, 2], [1, 2], 'array'],
            'boolean value' => ['bool', true, true, 'bool'],
            'float value' => ['float', 1.2023, 1.2023, 'float'],
            'integer value' => ['int', 1, 1, 'int'],
            'object value' => ['object', $object, $object, stdClass::class],
            'string value' => ['string', 'samdark', 'samdark', 'string'],
        ];
    }

    /**
     * @phpstan-return array<string, array{array<string, mixed>}>
     */
    public static function setValuesPayloads(): array
    {
        return [
            'native values' => [[
                'array' => [],
                'bool' => false,
                'float' => 1.434536,
                'int' => 1,
                'object' => new stdClass(),
                'string' => '',
            ]],
            'scalar values requiring casting' => [[
                'array' => [],
                'bool' => 'false',
                'float' => '1.434536',
                'int' => '1',
                'object' => new stdClass(),
                'string' => '',
            ]],
        ];
    }
}
