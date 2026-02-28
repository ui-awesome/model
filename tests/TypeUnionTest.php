<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use TypeError;
use UIAwesome\Model\Tests\Provider\TypeUnionProvider;
use UIAwesome\Model\Tests\Support\Model\UnionType;

/**
 * Unit tests for union-typed property handling on model values.
 *
 * Test coverage.
 * - Accepts all supported union member values and returns them without unintended transformations.
 * - Returns union type metadata in the expected declaration order.
 * - Throws a type error when assigning values outside the declared union.
 * - Validates whether a candidate type belongs to the declared union.
 *
 * {@see TypeUnionProvider} for test case data providers.
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class TypeUnionTest extends TestCase
{
    #[DataProviderExternal(TypeUnionProvider::class, 'isPropertyTypeChecks')]
    public function testReturnExpectedResultWhenCheckingUnionPropertyType(string $type, bool $expected): void
    {
        $model = new UnionType();

        self::assertSame(
            $expected,
            $model->isPropertyType('union', $type),
            'Should return the expected boolean result for union type membership checks.',
        );
    }

    public function testReturnUnionPropertyTypeMetadata(): void
    {
        $model = new UnionType();

        self::assertSame(
            [
                'union' => [
                    'object',
                    'string',
                    'int',
                    'bool',
                    'null',
                ],
            ],
            $model->getPropertyTypes(),
            'Should return the declared union member types for the property.',
        );
    }

    #[DataProviderExternal(TypeUnionProvider::class, 'acceptedUnionValues')]
    public function testReturnUnionValueForEachSupportedType(mixed $value, mixed $expected, string $expectedType): void
    {
        $model = new UnionType();

        $model->setPropertyValue('union', $value);

        self::assertSame(
            $expected,
            $model->getPropertyValue('union'),
            'Should preserve the assigned value for each union member type.',
        );
        self::assertSame(
            $expectedType,
            get_debug_type($model->getPropertyValue('union')),
            'Should preserve the expected runtime type for each union member value.',
        );
    }

    public function testThrowTypeErrorWhenAssigningUnsupportedUnionValue(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'Cannot assign array to property UIAwesome\Model\Tests\Support\Model\UnionType::$union of type object|string|int|bool|null',
        );

        $model = new UnionType();

        $model->setPropertyValue('union', []);
    }
}
