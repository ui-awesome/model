<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use InvalidArgumentException;
use NonNamespaced;
use PHPUnit\Framework\TestCase;
use stdClass;
use UIAwesome\Model\{
    AbstractModel,
    Tests\Support\Model\Address,
    Tests\Support\Model\Country,
    Tests\Support\Model\Profile,
    Tests\Support\Model\PropertyType
};

require __DIR__ . '/Support/Model/NonNamespaced.php';

final class ModelTest extends TestCase
{
    public function testGetData(): void
    {
        $model = new Country();

        self::assertTrue($model->load(['Country' => ['name' => 'Russia']]));
        self::assertSame(['name' => 'Russia'], $model->getData());
    }

    public function testGetModelName(): void
    {
        $model = new Country();

        self::assertSame('Country', $model->getModelName());

        $model = new class extends AbstractModel {};

        self::assertSame('', $model->getModelName());

        $model = new NonNamespaced();

        self::assertSame('NonNamespaced', $model->getModelName());
    }

    public function testGetProperties(): void
    {
        $model = new Country();

        self::assertSame(['name'], $model->getProperties());
    }

    public function testGetPropertyValue(): void
    {
        $model = new PropertyType();

        $model->setPropertyValue('array', [1, 2]);

        self::assertIsArray($model->getPropertyValue('array'));
        self::assertSame([1, 2], $model->getPropertyValue('array'));

        $model->setPropertyValue('bool', true);

        self::assertIsBool($model->getPropertyValue('bool'));
        self::assertSame(true, $model->getPropertyValue('bool'));

        $model->setPropertyValue('float', 1.2023);

        self::assertIsFloat($model->getPropertyValue('float'));
        self::assertSame(1.2023, $model->getPropertyValue('float'));

        $model->setPropertyValue('int', 1);

        self::assertIsInt($model->getPropertyValue('int'));
        self::assertSame(1, $model->getPropertyValue('int'));

        $model->setPropertyValue('object', new stdClass());

        self::assertIsObject($model->getPropertyValue('object'));
        self::assertInstanceOf(stdClass::class, $model->getPropertyValue('object'));

        $model->setPropertyValue('string', 'samdark');

        self::assertIsString($model->getPropertyValue('string'));
        self::assertSame('samdark', $model->getPropertyValue('string'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined property: "UIAwesome\Model\Tests\Support\Model\PropertyType::noExist');

        $model->getPropertyValue('noExist');
    }

    public function testHasProperty(): void
    {
        $model = new Country();

        self::assertTrue($model->hasProperty('name'));
    }

    public function testIsEmpty(): void
    {
        $model = new Country();

        self::assertTrue($model->isEmpty());
    }

    public function testLoad(): void
    {
        $model = new Country();

        self::assertTrue($model->load(['Country' => ['name' => 'Russia']]));
        self::assertSame('Russia', $model->getPropertyValue('name'));
    }

    public function testLoadPublicField(): void
    {
        $model = new PropertyType();

        self::assertEmpty($model->name);

        $data = [
            'PropertyType' => [
                'name' => 'samdark',
            ],
        ];

        self::assertTrue($model->load($data));
        self::assertSame('samdark', $model->name);
    }

    public function testLoadWithEmptyScope(): void
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

        self::assertIsInt($model->getPropertyValue('int'));
        self::assertIsFloat($model->getPropertyValue('float'));
        self::assertIsBool($model->getPropertyValue('bool'));
        self::assertIsString($model->getPropertyValue('string'));
    }

    public function testSetPropertiesValuesException(): void
    {
        $model = new PropertyType();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined property: "noExist".',
        );

        $model->setProperties(['noExist' => []]);
    }

    public function testSetPropertiesValueWithCamelCase(): void
    {
        $model = new Profile(new Address(new Country()));

        $model->setProperties(
            [
                'bio' => 'bio',
                'public_email_personal' => 'admin@example.com',
            ],
        );

        self::assertSame('bio', $model->getPropertyValue('bio'));
        self::assertSame('admin@example.com', $model->getPropertyValue('publicEmailPersonal'));
    }

    public function testSetPropertiesValueWithExceptColumns(): void
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

        self::assertSame('bio', $model->getPropertyValue('bio'));
        self::assertSame('', $model->getPropertyValue('publicEmailPersonal'));
    }

    public function testSetPropertyValue(): void
    {
        $model = new Country();

        $model->setPropertyValue('name', 'Russia');

        self::assertSame('Russia', $model->getPropertyValue('name'));
    }

    public function testSetPropiertiesValues(): void
    {
        $model = new PropertyType();

        // setPropertyValue attributes with array and to camel case disabled.
        $model->setProperties(
            [
                'array' => [],
                'bool' => false,
                'float' => 1.434536,
                'int' => 1,
                'object' => new stdClass(),
                'string' => '',
            ],
        );

        self::assertIsArray($model->getPropertyValue('array'));
        self::assertIsBool($model->getPropertyValue('bool'));
        self::assertIsFloat($model->getPropertyValue('float'));
        self::assertIsInt($model->getPropertyValue('int'));
        self::assertIsObject($model->getPropertyValue('object'));
        self::assertIsString($model->getPropertyValue('string'));

        // setPropertyValue attributes with array and to camel case enabled.
        $model->setProperties(
            [
                'array' => [],
                'bool' => 'false',
                'float' => '1.434536',
                'int' => '1',
                'object' => new stdClass(),
                'string' => '',
            ],
        );

        self::assertIsArray($model->getPropertyValue('array'));
        self::assertIsBool($model->getPropertyValue('bool'));
        self::assertIsFloat($model->getPropertyValue('float'));
        self::assertIsInt($model->getPropertyValue('int'));
        self::assertIsObject($model->getPropertyValue('object'));
        self::assertIsString($model->getPropertyValue('string'));
    }

    public function testToArray(): void
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
        );
    }

    public function testToArrayWithSnakeCase(): void
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
        );
    }
}
