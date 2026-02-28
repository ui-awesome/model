<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use PHPUnit\Framework\TestCase;
use TypeError;
use UIAwesome\Model\{Tests\Support\Model\Address, Tests\Support\Model\Country, Tests\Support\Model\PropertyType, TypeCollector};

final class TypeCollectorTest extends TestCase
{
    public function testGetProperties(): void
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
        );
    }

    public function testGetPropertiesTypes(): void
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
        );
    }

    public function testisPropertyType(): void
    {
        $model = new PropertyType();

        self::assertTrue($model->isPropertyType('array', 'array'));
        self::assertTrue($model->isPropertyType('bool', 'bool'));
        self::assertTrue($model->isPropertyType('float', 'float'));
        self::assertTrue($model->isPropertyType('int', 'int'));
        self::assertTrue($model->isPropertyType('nullable', 'int'));
        self::assertTrue($model->isPropertyType('nullable', 'null'));
        self::assertTrue($model->isPropertyType('object', 'object'));
        self::assertTrue($model->isPropertyType('object', 'null'));
        self::assertTrue($model->isPropertyType('string', 'string'));
        self::assertTrue($model->isPropertyType('withoutType', ''));
    }

    public function testPhpTypeCast(): void
    {
        $model = new PropertyType();

        $model->setPropertyValue('string', 1.1);
        $model->setPropertyValue('float', '1.1');

        self::assertSame('1.1', $model->getPropertyValue('string'));
        self::assertSame(1.1, $model->getPropertyValue('float'));
    }

    public function testPhpTypeCastAttributeNoExist(): void
    {
        $typeCollector = new TypeCollector(new PropertyType());

        self::assertNull($typeCollector->phpTypeCast('noExist', 1));
    }

    public function testPropertyStringable(): void
    {
        $model = new PropertyType();

        $objectStringable = new class {
            public function __toString(): string
            {
                return 'joe doe';
            }
        };
        $model->setPropertyValue('string', $objectStringable);

        self::assertSame('joe doe', $model->getPropertyValue('string'));
    }

    public function testSetPropertyValue(): void
    {
        $model = new PropertyType();

        // value is array
        $model->setPropertyValue('array', []);

        self::assertSame([], $model->getPropertyValue('array'));

        // value is string
        $model->setPropertyValue('string', 'string');

        self::assertSame('string', $model->getPropertyValue('string'));

        // value is int
        $model->setPropertyValue('int', 1);

        self::assertSame(1, $model->getPropertyValue('int'));

        // value is bool
        $model->setPropertyValue('bool', true);

        self::assertSame(true, $model->getPropertyValue('bool'));

        // value is null
        $model->setPropertyValue('object', null);

        self::assertNull($model->getPropertyValue('object'));

        // value is null
        $model->setPropertyValue('nullable', null);

        self::assertNull($model->getPropertyValue('nullable'));

        // value is int
        $model->setPropertyValue('nullable', 1);

        self::assertSame(1, $model->getPropertyValue('nullable'));

        // value is numeric string
        $model->setPropertyValue('nullable', '2');

        self::assertSame(2, $model->getPropertyValue('nullable'));

        // value is any
        $model->setPropertyValue('withoutType', 1);

        self::assertSame(1, $model->getPropertyValue('withoutType'));
    }

    public function testSetPropertyValueException(): void
    {
        $model = new PropertyType();

        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'Cannot assign array to property UIAwesome\Model\Tests\Support\Model\PropertyType::$string of type string',
        );

        $model->setPropertyValue('string', []);
    }

    public function testHasPropertyWithNestedPath(): void
    {
        $model = new Address(new Country());

        self::assertTrue($model->hasProperty('city'));
        self::assertTrue($model->hasProperty('country.name'));
    }

    public function testHasPropertyWithInvalidNestedPath(): void
    {
        $model = new Address(new Country());

        self::assertFalse($model->hasProperty('nonexistent.any'));
        self::assertFalse($model->hasProperty('city.any'));
        self::assertFalse($model->hasProperty('country.nonexistent'));
    }

    public function testToArrayWithSnakeCaseForPascalCaseProperty(): void
    {
        $model = new PropertyType();

        $model->addProperty('Name', 'string');
        $model->setPropertyValue('Name', 'joe');

        self::assertArrayHasKey('name', $model->toArray(true));
        self::assertSame('joe', $model->toArray(true)['name']);
    }
}
