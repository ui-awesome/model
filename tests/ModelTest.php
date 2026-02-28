<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use InvalidArgumentException;
use NonNamespaced;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use stdClass;
use UIAwesome\Model\{
    AbstractModel,
    Tests\Provider\ModelProvider,
    Tests\Support\Model\Address,
    Tests\Support\Model\Country,
    Tests\Support\Model\Profile,
    Tests\Support\Model\PropertyType
};

require __DIR__ . '/Support/Model/NonNamespaced.php';

/**
 * Unit tests for the {@see AbstractModel} behavior through concrete and anonymous test models.
 *
 * Test coverage.
 * - Converts model data to `array` format with exclusions and optional snake_case keys.
 * - Loads scoped and unscoped payloads while preserving typed property casting behavior.
 * - Resolves model names for namespaced, anonymous, and non-namespaced models.
 * - Returns model metadata, including loaded data and declared property names.
 * - Sets individual and bulk properties, including snake_case to camelCase mapping and exclusion lists.
 * - Throws invalid argument exceptions for undefined properties during read and write operations.
 * - Verifies property existence and empty-state behavior on new model instances.
 *
 * {@see ModelProvider} for test case data providers.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class ModelTest extends TestCase
{
    public function testCastValuesWhenLoadingWithEmptyScope(): void
    {
        $model = new class extends AbstractModel {
            private int $int = 1;
            private string $string = 'string';
            private float $float = 3.14;
            private bool $bool = true;
        };

        $model->load([
            'int' => '2',
            'float' => '3.15',
            'bool' => 'false',
            'string' => '555',
        ], '');

        self::assertSame('int', get_debug_type($model->getPropertyValue('int')), 'Should cast integer strings to int.');
        self::assertSame('float', get_debug_type($model->getPropertyValue('float')), 'Should cast numeric strings to float.');
        self::assertSame('bool', get_debug_type($model->getPropertyValue('bool')), 'Should cast boolean-like strings to bool.');
        self::assertSame('string', get_debug_type($model->getPropertyValue('string')), 'Should keep string values as string.');
    }

    public function testLoadDataIntoModelUsingDefaultScope(): void
    {
        $model = new Country();

        self::assertTrue($model->load(['Country' => ['name' => 'Russia']]), 'Should load data using the model class scope.');
        self::assertSame('Russia', $model->getPropertyValue('name'), 'Should set the property value from loaded data.');
    }

    public function testLoadPublicPropertyWhenDefinedOnModel(): void
    {
        $model = new PropertyType();

        self::assertEmpty($model->name, 'Should start with an empty public property value.');

        $data = [
            'PropertyType' => [
                'name' => 'samdark',
            ],
        ];

        self::assertTrue($model->load($data), 'Should load values for public properties.');
        self::assertSame('samdark', $model->name, 'Should set the loaded value on the public property.');
    }

    public function testReturnArrayWithoutExcludedProperties(): void
    {
        $address = new Address(new Country());
        $model = new Profile($address);

        $model->setProperties(
            [
                'bio' => 'bio',
                'publicEmailPersonal' => 'admin@example.com',
            ],
        );

        self::assertSame(
            [
                'bio' => 'bio',
                'publicEmailPersonal' => 'admin@example.com',
                'address' => $address,
            ],
            $model->toArray(exceptProperties: ['pathAvatar']),
            'Should convert model data to an array excluding specified properties.',
        );
    }

    public function testReturnDeclaredProperties(): void
    {
        $model = new Country();

        self::assertSame(['name'], $model->getProperties(), 'Should return declared model properties in definition order.');
    }
    public function testReturnLoadedDataAfterSuccessfulLoad(): void
    {
        $model = new Country();

        self::assertTrue(
            $model->load(['Country' => ['name' => 'Russia']]),
            'Should return true when loading data with the model scope.',
        );
        self::assertSame(
            ['name' => 'Russia'],
            $model->getData(),
            'Should return the data loaded for the current model scope.',
        );
    }

    public function testReturnModelNameForNamespacedAnonymousAndNonNamespacedModels(): void
    {
        $model = new Country();

        self::assertSame('Country', $model->getModelName(), 'Should return the short class name for namespaced models.');

        $model = new class extends AbstractModel {};

        self::assertSame('', $model->getModelName(), 'Should return an empty name for anonymous models.');

        $model = new NonNamespaced();

        self::assertSame(
            'NonNamespaced',
            $model->getModelName(),
            'Should return the class name for non-namespaced models.',
        );
    }

    public function testReturnSnakeCaseKeysWhenConvertingToArray(): void
    {
        $address = new Address(new Country());
        $model = new Profile($address);

        $model->setProperties(
            [
                'bio' => 'bio',
                'publicEmailPersonal' => 'admin@example.com',
            ],
        );

        self::assertSame(
            [
                'bio' => 'bio',
                'public_email_personal' => 'admin@example.com',
                'address' => $address,
            ],
            $model->toArray(true, ['pathAvatar']),
            'Should convert property keys to snake_case while excluding specified properties.',
        );
    }

    public function testReturnTrueWhenModelHasNoAssignedValues(): void
    {
        $model = new Country();

        self::assertTrue($model->isEmpty(), 'Should return true when all model properties are empty.');
    }

    public function testReturnTrueWhenPropertyExists(): void
    {
        $model = new Country();

        self::assertTrue($model->hasProperty('name'), 'Should return true for an existing property.');
    }

    #[DataProviderExternal(ModelProvider::class, 'propertyValueAssignments')]
    public function testReturnTypedPropertyValueForSupportedTypes(
        string $property,
        mixed $value,
        mixed $expected,
        string $expectedType,
    ): void {
        $model = new PropertyType();

        $model->setPropertyValue($property, $value);

        self::assertSame(
            $expected,
            $model->getPropertyValue($property),
            'Should return the exact value assigned to the property.',
        );
        self::assertSame(
            $expectedType,
            get_debug_type($model->getPropertyValue($property)),
            'Should preserve the expected runtime type for the property value.',
        );
    }

    #[DataProviderExternal(ModelProvider::class, 'setPropertiesPayloads')]
    public function testSetPropertiesForNativeAndCastableValues(array $properties): void
    {
        $model = new PropertyType();

        $model->setProperties($properties);

        self::assertSame('array', get_debug_type($model->getPropertyValue('array')), 'Should keep array properties as arrays.');
        self::assertSame('bool', get_debug_type($model->getPropertyValue('bool')), 'Should cast boolean properties correctly.');
        self::assertSame('float', get_debug_type($model->getPropertyValue('float')), 'Should cast float properties correctly.');
        self::assertSame('int', get_debug_type($model->getPropertyValue('int')), 'Should cast integer properties correctly.');
        self::assertSame(
            stdClass::class,
            get_debug_type($model->getPropertyValue('object')),
            'Should keep object properties as objects.',
        );
        self::assertSame('string', get_debug_type($model->getPropertyValue('string')), 'Should keep string properties as strings.');
    }

    public function testSetPropertiesUsingSnakeCaseInputMappedToCamelCase(): void
    {
        $model = new Profile(new Address(new Country()));

        $model->setProperties(
            [
                'bio' => 'bio',
                'public_email_personal' => 'admin@example.com',
            ],
        );

        self::assertSame('bio', $model->getPropertyValue('bio'), 'Should set direct property values from input.');
        self::assertSame(
            'admin@example.com',
            $model->getPropertyValue('publicEmailPersonal'),
            'Should map snake_case input keys to camelCase model properties.',
        );
    }

    public function testSetSinglePropertyValue(): void
    {
        $model = new Country();

        $model->setPropertyValue('name', 'Russia');

        self::assertSame('Russia', $model->getPropertyValue('name'), 'Should set and return the assigned property value.');
    }

    public function testSkipExceptedPropertiesWhenSettingValues(): void
    {
        $model = new Profile(new Address(new Country()));
        $model->setProperties(
            [
                'bio' => 'bio',
                'public_email_personal' => 'admin@example.com',
            ],
            [
                'publicEmailPersonal',
            ],
        );

        self::assertSame('bio', $model->getPropertyValue('bio'), 'Should set values for properties not listed in exclusions.');
        self::assertSame(
            '',
            $model->getPropertyValue('publicEmailPersonal'),
            'Should skip assigning values for excluded properties.',
        );
    }

    public function testThrowInvalidArgumentExceptionWhenGettingUndefinedPropertyValue(): void
    {
        $model = new PropertyType();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined property: "UIAwesome\Model\Tests\Support\Model\PropertyType::noExist');

        $model->getPropertyValue('noExist');
    }

    public function testThrowInvalidArgumentExceptionWhenSettingUndefinedProperty(): void
    {
        $model = new PropertyType();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined property: "noExist".',
        );

        $model->setProperties(['noExist' => []]);
    }
}
