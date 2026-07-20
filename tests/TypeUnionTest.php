<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use PHPUnit\Framework\Attributes\{DataProviderExternal, Group};
use PHPUnit\Framework\TestCase;
use TypeError;
use UIAwesome\Model\Tests\Provider\TypeUnionProvider;
use UIAwesome\Model\Tests\Support\Model\{Dynamic, UnionType};

use function get_debug_type;

/**
 * Unit tests for union-typed property handling on model values.
 *
 * {@see TypeUnionProvider} for test case data providers.
 */
#[Group('type-union')]
final class TypeUnionTest extends TestCase
{
    public function testCastSingleNonNullUnionMemberWhenNullIsDeclaredFirst(): void
    {
        $model = new Dynamic();

        $model->add('value', ['null', 'int']);
        $model->setValue('value', '5');

        self::assertSame(
            5,
            $model->getValue('value'),
            'Single non-null union member must be applied for casting regardless of key position.',
        );
        self::assertSame(
            'int',
            get_debug_type($model->getValue('value')),
            'Value must be cast to the only non-null union member type.',
        );
    }

    public function testDoNotCastWhenMultipleNonNullUnionMembersExist(): void
    {
        $model = new Dynamic();

        $model->add('value', ['int', 'string']);
        $model->setValue('value', '5');

        self::assertSame(
            '5',
            $model->getValue('value'),
            'Ambiguous non-null union types must leave the value uncast.',
        );
        self::assertSame(
            'string',
            get_debug_type($model->getValue('value')),
            'Value must keep its original runtime type when casting is ambiguous.',
        );
    }

    #[DataProviderExternal(TypeUnionProvider::class, 'isTypeChecks')]
    public function testReturnExpectedResultWhenCheckingUnionPropertyType(string $type, bool $expected): void
    {
        $model = new UnionType();

        self::assertSame(
            $expected,
            $model->isType('union', $type),
            'Should return the expected boolean result for union type membership checks.',
        );
    }

    public function testReturnFalseWhenNestedPathTargetsUnionProperty(): void
    {
        $model = new UnionType();

        self::assertFalse(
            $model->has('union.value'),
            'Union-typed properties must not be treated as nested model paths.',
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
            $model->getTypes(),
            'Should return the declared union member types for the property.',
        );
    }

    #[DataProviderExternal(TypeUnionProvider::class, 'acceptedUnionValues')]
    public function testReturnUnionValueForEachSupportedType(mixed $value, mixed $expected, string $expectedType): void
    {
        $model = new UnionType();

        $model->setValue('union', $value);

        self::assertSame(
            $expected,
            $model->getValue('union'),
            'Should preserve the assigned value for each union member type.',
        );
        self::assertSame(
            $expectedType,
            get_debug_type($model->getValue('union')),
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

        $model->setValue('union', []);
    }
}
