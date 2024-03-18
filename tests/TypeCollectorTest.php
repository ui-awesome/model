<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use UIAwesome\Model\{Tests\Support\Model\PropertyType, TypeCollector};

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class TypeCollectorTest extends \PHPUnit\Framework\TestCase
{
    public function testGetProperties(): void
    {
        $model = new PropertyType();

        $this->assertSame(
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
            $model->getProperties()
        );
    }

    public function testGetPropertiesTypes(): void
    {
        $model = new PropertyType();

        $this->assertSame(
            [
                'name' => 'string',
                'array' => 'array',
                'bool' => 'bool',
                'float' => 'float',
                'int' => 'int',
                'nullable' => 'int',
                'object' => 'object',
                'string' => 'string',
                'withoutType' => '',
            ],
            $model->getPropertiesTypes()
        );
    }

    public function testIsPropertyType(): void
    {
        $model = new PropertyType();

        $this->assertTrue($model->ispropertyType('array', 'array'));
        $this->assertTrue($model->ispropertyType('bool', 'bool'));
        $this->assertTrue($model->ispropertyType('float', 'float'));
        $this->assertTrue($model->ispropertyType('int', 'int'));
        $this->assertTrue($model->ispropertyType('nullable', 'int'));
        $this->assertTrue($model->ispropertyType('object', 'object'));
        $this->assertTrue($model->ispropertyType('string', 'string'));
        $this->assertTrue($model->ispropertyType('withoutType', ''));
    }

    public function testPhpTypeCast(): void
    {
        $model = new PropertyType();

        $model->setPropertyValue('string', 1.1);
        $model->setPropertyValue('float', '1.1');

        $this->assertSame('1.1', $model->getPropertyValue('string'));
        $this->assertSame(1.1, $model->getPropertyValue('float'));
    }

    public function testPhpTypeCastAttributeNoExist(): void
    {
        $typeCollector = new TypeCollector(new PropertyType());

        $this->assertNull($typeCollector->phpTypeCast('noExist', 1));
    }

    public function testPropertyStringable(): void
    {
        $model = new PropertyType();

        $objectStringable = new class () {
            public function __toString(): string
            {
                return 'joe doe';
            }
        };
        $model->setPropertyValue('string', $objectStringable);

        $this->assertSame('joe doe', $model->getPropertyValue('string'));
    }

    public function testSetPropertyValue(): void
    {
        $model = new PropertyType();

        // value is array
        $model->setPropertyValue('array', []);

        $this->assertSame([], $model->getPropertyValue('array'));

        // value is string
        $model->setPropertyValue('string', 'string');

        $this->assertSame('string', $model->getPropertyValue('string'));

        // value is int
        $model->setPropertyValue('int', 1);

        $this->assertSame(1, $model->getPropertyValue('int'));

        // value is bool
        $model->setPropertyValue('bool', true);

        $this->assertSame(true, $model->getPropertyValue('bool'));

        // value is null
        $model->setPropertyValue('object', null);

        $this->assertNull($model->getPropertyValue('object'));

        // value is null
        $model->setPropertyValue('nullable', null);

        $this->assertNull($model->getPropertyValue('nullable'));

        // value is int
        $model->setPropertyValue('nullable', 1);

        $this->assertSame(1, $model->getPropertyValue('nullable'));

        // value is any
        $model->setPropertyValue('withoutType', 1);

        $this->assertSame(1, $model->getPropertyValue('withoutType'));
    }

    public function testSetPropertyValueException(): void
    {
        $model = new PropertyType();

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Cannot assign array to property UIAwesome\Model\Tests\Support\Model\PropertyType::$string of type string',
        );

        $model->setPropertyValue('string', []);
    }
}
