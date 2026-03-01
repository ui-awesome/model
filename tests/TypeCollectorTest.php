<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use TypeError;
use UIAwesome\Model\Exception\Message;
use UIAwesome\Model\Tests\Provider\TypeCollectorProvider;
use UIAwesome\Model\Tests\Support\Contract\{IntersectionLeft, IntersectionRight};
use UIAwesome\Model\Tests\Support\Model\{Address, Country, DateTimeType, IntersectionType, PropertyType, ReadonlyState};
use UIAwesome\Model\TypeCollector;

/**
 * Unit tests for property collection and runtime type casting via {@see TypeCollector}.
 *
 * Test coverage.
 * - Casts values to declared PHP property types, including stringable objects and nullable typed properties.
 * - Detects property presence for flat and nested property paths.
 * - Resolves intersection-typed properties into the expected named type metadata.
 * - Returns null for type casting requests targeting unknown properties.
 * - Returns property names and type metadata collected from model declarations.
 * - Throws type errors when assigned values violate declared property types.
 *
 * {@see TypeCollectorProvider} for test case data providers.
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class TypeCollectorTest extends TestCase
{
    public function testCastNullableIntPropertyWithPhpTypeCast(): void
    {
        $typeCollector = new TypeCollector(new PropertyType());

        $castValue = $typeCollector->phpTypeCast('nullable', '2');

        self::assertSame(
            2,
            $castValue,
            'Should resolve nullable int property to the non-null member type for casting.',
        );
        self::assertIsInt(
            $castValue,
            'Should return int runtime type when nullable int property receives numeric string.',
        );
    }

    public function testCastPrimitiveValuesWithPhpTypeCast(): void
    {
        $typeCollector = new TypeCollector(new PropertyType());

        self::assertFalse(
            $typeCollector->phpTypeCast('bool', 0),
            'Should cast numeric zero to false for bool properties.',
        );

        $floatValue = $typeCollector->phpTypeCast('float', '1.5');
        self::assertSame(
            1.5,
            $floatValue,
            'Should cast numeric strings to float for float properties.',
        );
        self::assertIsFloat(
            $floatValue,
            'Should preserve float runtime type after casting.',
        );

        $intValue = $typeCollector->phpTypeCast('int', '2');
        self::assertSame(
            2,
            $intValue,
            'Should cast numeric strings to int for int properties.',
        );
        self::assertIsInt(
            $intValue,
            'Should preserve int runtime type after casting.',
        );

        self::assertSame(
            '10',
            $typeCollector->phpTypeCast('string', 10),
            'Should cast scalar values to string for string properties.',
        );
    }

    public function testCastStringableObjectToStringProperty(): void
    {
        $model = new PropertyType();

        $objectStringable = new class {
            public function __toString(): string
            {
                return 'joe doe';
            }
        };

        $model->setPropertyValue('string', $objectStringable);

        self::assertSame(
            'joe doe',
            $model->getPropertyValue('string'),
            'Should cast stringable objects to string properties.',
        );
    }

    public function testCastStringToDateTimeImmutableProperty(): void
    {
        $model = new DateTimeType();

        $model->setProperties(
            [
                'updatedAt' => '2026-02-28T10:30:00+00:00',
                'publishedAt' => '2026-02-28T12:00:00+00:00',
            ],
        );
        self::assertSame(
            '2026-02-28T10:30:00+00:00',
            $model->getPropertyValue('updatedAt')->format('Y-m-d\TH:i:sP'),
            'Should preserve updatedAt timestamp and timezone after casting.',
        );
        self::assertInstanceOf(
            DateTimeImmutable::class,
            $model->getPropertyValue('updatedAt'),
            'Should cast ISO-8601 strings to DateTimeImmutable objects for typed properties.',
        );
        self::assertSame(
            '2026-02-28T12:00:00+00:00',
            $model->getPropertyValue('publishedAt')->format('Y-m-d\TH:i:sP'),
            'Should preserve publishedAt timestamp and timezone after casting.',
        );
        self::assertInstanceOf(
            DateTimeImmutable::class,
            $model->getPropertyValue('publishedAt'),
            'Should cast nullable DateTimeImmutable properties when non-null strings are provided.',
        );
    }

    public function testCastStringToDateTimeProperty(): void
    {
        $model = new DateTimeType();

        $model->setPropertyValue('createdAt', '2026-02-28 10:30:00');

        $createdAt = $model->getPropertyValue('createdAt');

        self::assertInstanceOf(
            DateTime::class,
            $createdAt,
            'Should cast valid date strings to DateTime objects when the property type requires it.',
        );
        self::assertSame(
            '2026-02-28 10:30:00',
            $createdAt->format('Y-m-d H:i:s'),
            'Should preserve the parsed timestamp value after DateTime casting.',
        );
    }

    public function testCastValueToDeclaredPhpType(): void
    {
        $model = new PropertyType();

        $model->setPropertyValue('string', 1.1);
        $model->setPropertyValue('float', '1.1');

        self::assertSame(
            '1.1',
            $model->getPropertyValue('string'),
            'Should cast numeric values assigned to string properties.',
        );
        self::assertSame(
            1.1,
            $model->getPropertyValue('float'),
            'Should cast numeric strings assigned to float properties.',
        );
    }

    public function testKeepDateTimeObjectInstanceWhenAlreadyTyped(): void
    {
        $model = new DateTimeType();
        $dateTime = new DateTime('2026-02-28 14:00:00');

        $model->setPropertyValue('createdAt', $dateTime);

        self::assertSame(
            $dateTime,
            $model->getPropertyValue('createdAt'),
            'Should keep existing DateTime object instances without rebuilding them.',
        );
    }

    public function testReturnCollectedIntersectionPropertyType(): void
    {
        $model = new IntersectionType();

        self::assertSame(
            [
                'intersection' => [IntersectionLeft::class, IntersectionRight::class],
            ],
            $model->getPropertyTypes(),
            'Should collect all named members from intersection-typed properties.',
        );
    }

    public function testReturnCollectedPropertyNames(): void
    {
        $model = new PropertyType();

        self::assertSame(
            [
                'name',
                'array',
                'bool',
                'float',
                'int',
                'nullable',
                'object',
                'string',
                'withoutType',
            ],
            $model->getProperties(),
            'Should return all declared property names collected from the model.',
        );
    }

    public function testReturnCollectedPropertyTypes(): void
    {
        $model = new PropertyType();

        self::assertSame(
            [
                'name' => 'string',
                'array' => 'array',
                'bool' => 'bool',
                'float' => 'float',
                'int' => 'int',
                'nullable' => ['int', 'null'],
                'object' => ['object', 'null'],
                'string' => 'string',
                'withoutType' => '',
            ],
            $model->getPropertyTypes(),
            'Should return collected property types including union and untyped properties.',
        );
    }

    #[DataProviderExternal(TypeCollectorProvider::class, 'isPropertyTypeChecks')]
    public function testReturnExpectedResultWhenCheckingPropertyType(string $property, string $type, bool $expected): void
    {
        $model = new PropertyType();

        self::assertSame(
            $expected,
            $model->isPropertyType($property, $type),
            'Should return the expected boolean result when checking supported property types.',
        );
    }

    public function testReturnFalseWhenNestedPathTargetsNonModelTypedDynamicProperty(): void
    {
        $model = new PropertyType();

        $model->addProperty('profile', 'string');
        $model->setPropertyValue('profile', new Address(new Country()));

        self::assertFalse(
            $model->hasProperty('profile.city'),
            'Should return false when nested lookup starts from a property not declared as a model type.',
        );
    }

    public function testReturnFalseWhenNestedPathValueIsNotModelInstance(): void
    {
        $model = new PropertyType();

        $model->addProperty('profile', Address::class);
        $model->setPropertyValue('profile', 'not-a-model');

        self::assertFalse(
            $model->hasProperty('profile.city'),
            'Should return false when the nested root value is not a model instance.',
        );
    }

    public function testReturnFalseWhenNestedPropertyPathIsInvalid(): void
    {
        $model = new Address(new Country());

        self::assertFalse(
            $model->hasProperty('nonexistent.any'),
            'Should return false when the root nested segment is missing.',
        );
        self::assertFalse(
            $model->hasProperty('city.any'),
            'Should return false when a scalar property is used as a nested branch.',
        );
        self::assertFalse(
            $model->hasProperty('country.nonexistent'),
            'Should return false when a nested property segment does not exist.',
        );
    }

    public function testReturnNullWhenCastingNullValueForKnownProperty(): void
    {
        $typeCollector = new TypeCollector(new PropertyType());

        self::assertNull(
            $typeCollector->phpTypeCast('bool', null),
            'Should return null when casting null for a known property.',
        );
    }

    public function testReturnNullWhenCastingUnknownProperty(): void
    {
        $typeCollector = new TypeCollector(new PropertyType());

        self::assertNull(
            $typeCollector->phpTypeCast('noExist', 1),
            'Should return null when a property does not exist.',
        );
    }

    public function testReturnNullWhenCastingUnknownPropertyWithArrayValue(): void
    {
        $typeCollector = new TypeCollector(new PropertyType());

        self::assertNull(
            $typeCollector->phpTypeCast('noExist', []),
            'Should return null when casting an array value for an unknown property.',
        );
    }

    public function testReturnSnakeCaseKeyForPascalCasePropertyWhenConvertingToArray(): void
    {
        $model = new PropertyType();

        $model->addProperty('Name', 'string');
        $model->setPropertyValue('Name', 'joe');

        self::assertArrayHasKey(
            'name',
            $model->toArray(true),
            'Should expose PascalCase properties as snake_case keys.',
        );
        self::assertSame(
            'joe',
            $model->toArray(true)['name'],
            'Should keep the assigned value for converted snake_case keys.',
        );
    }

    public function testReturnTrueWhenPropertyPathExists(): void
    {
        $model = new Address(new Country());

        self::assertTrue(
            $model->hasProperty('city'),
            'Should return true for an existing flat property.',
        );
        self::assertTrue(
            $model->hasProperty('country.name'),
            'Should return true for an existing nested property path.',
        );
    }

    #[DataProviderExternal(TypeCollectorProvider::class, 'setPropertyValueCases')]
    public function testSetPropertyValueWithSupportedInputs(string $property, mixed $value, mixed $expected): void
    {
        $model = new PropertyType();

        $model->setPropertyValue($property, $value);

        self::assertSame(
            $expected,
            $model->getPropertyValue($property),
            'Should assign and return the expected value for each supported property input case.',
        );
    }

    public function testSetReadonlyPropertyWhenUninitialized(): void
    {
        $model = new ReadonlyState(new Country());

        $model->setPropertyValue('token', 'phase-2');

        self::assertSame(
            'phase-2',
            $model->getPropertyValue('token'),
            'Should allow assigning an uninitialized readonly property once.',
        );
    }

    public function testThrowInvalidArgumentExceptionWhenCastingDateTimeString(): void
    {
        $model = new DateTimeType();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::INVALID_DATE_TIME_STRING->getMessage('not-a-date', DateTime::class),
        );

        $model->setPropertyValue('createdAt', 'not-a-date');
    }

    public function testThrowInvalidArgumentExceptionWhenCastingOverflowDateTimeString(): void
    {
        $model = new DateTimeType();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::INVALID_DATE_TIME_STRING->getMessage('2026-02-30 10:30:00', DateTime::class),
        );

        $model->setPropertyValue('createdAt', '2026-02-30 10:30:00');
    }

    public function testThrowInvalidArgumentExceptionWhenOverwritingInitializedReadonlyProperty(): void
    {
        $model = new ReadonlyState(new Country());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::READONLY_PROPERTY_ALREADY_INITIALIZED->getMessage(
                ReadonlyState::class,
                'country',
            ),
        );

        $model->setPropertyValue('country', new Country());
    }

    public function testThrowInvalidArgumentExceptionWhenReassigningReadonlyPropertyAfterFirstInitialization(): void
    {
        $model = new ReadonlyState(new Country());

        $model->setPropertyValue('token', 'phase-2');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::READONLY_PROPERTY_ALREADY_INITIALIZED->getMessage(
                ReadonlyState::class,
                'token',
            ),
        );

        $model->setPropertyValue('token', 'phase-2-update');
    }

    public function testThrowInvalidArgumentExceptionWhenReassigningReadonlyPropertyViaSetProperties(): void
    {
        $model = new ReadonlyState(new Country());

        $model->setProperties(['token' => 'phase-1']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::READONLY_PROPERTY_ALREADY_INITIALIZED->getMessage(
                ReadonlyState::class,
                'token',
            ),
        );

        $model->setProperties(['token' => 'phase-2']);
    }

    public function testThrowTypeErrorWhenAssigningInvalidPropertyValue(): void
    {
        $model = new PropertyType();

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'Cannot assign array to property UIAwesome\Model\Tests\Support\Model\PropertyType::$string of type string',
        );

        $model->setPropertyValue('string', []);
    }

    public function testWriteDynamicPropertyOnlyToCollectorStorage(): void
    {
        $model = new PropertyType();

        $model->addProperty('dynamicFlag', 'bool');
        $model->setPropertyValue('dynamicFlag', true);

        self::assertTrue(
            $model->getPropertyValue('dynamicFlag'),
            'Should store and return values assigned to dynamic properties.',
        );
        self::assertFalse(
            property_exists($model, 'dynamicFlag'),
            'Should not create runtime dynamic properties on the model instance.',
        );
    }
}
