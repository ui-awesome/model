<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use TypeError;
use UIAwesome\Model\Tests\Provider\TypeCollectorProvider;
use UIAwesome\Model\Tests\Support\Model\{Address, Country, PropertyType};
use UIAwesome\Model\TypeCollector;

/**
 * Unit tests for property collection and runtime type casting via {@see TypeCollector}.
 *
 * Test coverage.
 * - Casts values to declared PHP property types, including stringable objects and nullable typed properties.
 * - Detects property presence for flat and nested property paths.
 * - Returns null for type casting requests targeting unknown properties.
 * - Returns property names and type metadata collected from model declarations.
 * - Throws type errors when assigned values violate declared property types.
 *
 * {@see TypeCollectorProvider} for test case data providers.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class TypeCollectorTest extends TestCase
{
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

    public function testReturnNullWhenCastingNullValueForKnownProperty(): void
    {
        $typeCollector = new TypeCollector(new PropertyType());

        self::assertNull(
            $typeCollector->phpTypeCast('bool', null),
            'Should return null when casting null for a known property.',
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
