<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Provider;

use stdClass;

/**
 * Data provider for {@see \UIAwesome\Model\Tests\TypeUnionTest} test cases.
 *
 * Provides representative input/output pairs for union property type checks and accepted union values.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class TypeUnionProvider
{
    /**
     * @phpstan-return array<string, array{string, bool}>
     */
    public static function isPropertyTypeChecks(): array
    {
        return [
            'union does not accept datetime' => ['datetime', false],
            'union accepts bool' => ['bool', true],
            'union accepts int' => ['int', true],
            'union accepts null' => ['null', true],
            'union accepts object' => ['object', true],
            'union accepts string' => ['string', true],
        ];
    }

    /**
     * @phpstan-return array<string, array{mixed, mixed, string}>
     */
    public static function acceptedUnionValues(): array
    {
        $object = new stdClass();

        return [
            'integer value' => [1, 1, 'int'],
            'string value' => ['1', '1', 'string'],
            'boolean value' => [true, true, 'bool'],
            'object value' => [$object, $object, stdClass::class],
            'null value' => [null, null, 'null'],
        ];
    }
}
