<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use NonNamespaced;
use stdClass;
use UIAwesome\Model\{
    AbstractModel,
    Tests\Support\Model\Address,
    Tests\Support\Model\Country,
    Tests\Support\Model\Profile,
    Tests\Support\Model\PropertyType
};

require __DIR__ . '/Support/Model/NonNamespaced.php';

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ModelTest extends \PHPUnit\Framework\TestCase
{
    public function testGetData(): void
    {
        $model = new Country();

        $this->assertTrue($model->load(['Country' => ['name' => 'Russia']]));
        $this->assertSame(['name' => 'Russia'], $model->getData());
    }

    public function testGetModelName(): void
    {
        $model = new Country();

        $this->assertSame('Country', $model->getModelName());

        $model = new class () extends AbstractModel {};

        $this->assertSame('', $model->getModelName());

        $model = new NonNamespaced();

        $this->assertSame('NonNamespaced', $model->getModelName());
    }

    public function testGetProperties(): void
    {
        $model = new Country();

        $this->assertSame(['name'], $model->getProperties());
    }

    public function testGetPropertyValue(): void
    {
        $model = new PropertyType();

        $model->setPropertyValue('array', [1, 2]);

        $this->assertIsArray($model->getPropertyValue('array'));
        $this->assertSame([1, 2], $model->getPropertyValue('array'));

        $model->setPropertyValue('bool', true);

        $this->assertIsBool($model->getPropertyValue('bool'));
        $this->assertSame(true, $model->getPropertyValue('bool'));

        $model->setPropertyValue('float', 1.2023);

        $this->assertIsFloat($model->getPropertyValue('float'));
        $this->assertSame(1.2023, $model->getPropertyValue('float'));

        $model->setPropertyValue('int', 1);

        $this->assertIsInt($model->getPropertyValue('int'));
        $this->assertSame(1, $model->getPropertyValue('int'));

        $model->setPropertyValue('object', new stdClass());

        $this->assertIsObject($model->getPropertyValue('object'));
        $this->assertInstanceOf(stdClass::class, $model->getPropertyValue('object'));

        $model->setPropertyValue('string', 'samdark');

        $this->assertIsString($model->getPropertyValue('string'));
        $this->assertSame('samdark', $model->getPropertyValue('string'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined property: "UIAwesome\Model\Tests\Support\Model\PropertyType::noExist');

        $model->getPropertyValue('noExist');
    }

    public function testHasProperty(): void
    {
        $model = new Country();

        $this->assertTrue($model->hasProperty('name'));
    }

    public function testIsEmpty(): void
    {
        $model = new Country();

        $this->assertTrue($model->isEmpty());
    }

    public function testLoad(): void
    {
        $model = new Country();

        $this->assertTrue($model->load(['Country' => ['name' => 'Russia']]));
        $this->assertSame('Russia', $model->getPropertyValue('name'));
    }

    public function testLoadPublicField(): void
    {
        $model = new PropertyType();

        $this->assertEmpty($model->name);

        $data = [
            'PropertyType' => [
                'name' => 'samdark',
            ],
        ];

        $this->assertTrue($model->load($data));
        $this->assertSame('samdark', $model->name);
    }

    public function testLoadWithEmptyScope(): void
    {
        $model = new class () extends AbstractModel {
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

        $this->assertIsInt($model->getPropertyValue('int'));
        $this->assertIsFloat($model->getPropertyValue('float'));
        $this->assertIsBool($model->getPropertyValue('bool'));
        $this->assertIsString($model->getPropertyValue('string'));
    }

    public function testSetPropiertiesValues(): void
    {
        $model = new PropertyType();

        // setPropertyValue attributes with array and to camel case disabled.
        $model->setPropertiesValues(
            [
                'array' => [],
                'bool' => false,
                'float' => 1.434536,
                'int' => 1,
                'object' => new stdClass(),
                'string' => '',
            ],
        );

        $this->assertIsArray($model->getPropertyValue('array'));
        $this->assertIsBool($model->getPropertyValue('bool'));
        $this->assertIsFloat($model->getPropertyValue('float'));
        $this->assertIsInt($model->getPropertyValue('int'));
        $this->assertIsObject($model->getPropertyValue('object'));
        $this->assertIsString($model->getPropertyValue('string'));

        // setPropertyValue attributes with array and to camel case enabled.
        $model->setPropertiesValues(
            [
                'array' => [],
                'bool' => 'false',
                'float' => '1.434536',
                'int' => '1',
                'object' => new stdClass(),
                'string' => '',
            ],
        );

        $this->assertIsArray($model->getPropertyValue('array'));
        $this->assertIsBool($model->getPropertyValue('bool'));
        $this->assertIsFloat($model->getPropertyValue('float'));
        $this->assertIsInt($model->getPropertyValue('int'));
        $this->assertIsObject($model->getPropertyValue('object'));
        $this->assertIsString($model->getPropertyValue('string'));
    }

    public function testSetPropertiesValuesException(): void
    {
        $model = new PropertyType();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Undefined property: "noExist".'
        );

        $model->setPropertiesValues(['noExist' => []]);
    }

    public function testSetPropertiesValueWithCamelCase(): void
    {
        $model = new Profile(new Address(new Country()));

        $model->setPropertiesValues(
            [
                'bio' => 'bio',
                'public_email_personal' => 'admin@example.com',
            ],
        );

        $this->assertSame('bio', $model->getPropertyValue('bio'));
        $this->assertSame('admin@example.com', $model->getPropertyValue('publicEmailPersonal'));
    }

    public function testSetPropertiesValueWithExceptColumns(): void
    {
        $model = new Profile(new Address(new Country()));
        $model->setPropertiesValues(
            [
                'bio' => 'bio',
                'public_email_personal' => 'admin@example.com',
            ],
            [
                'public_email_personal',
            ]
        );

        $this->assertSame('bio', $model->getPropertyValue('bio'));
        $this->assertSame('', $model->getPropertyValue('publicEmailPersonal'));
    }

    public function testSetPropertyValue(): void
    {
        $model = new Country();

        $model->setPropertyValue('name', 'Russia');

        $this->assertSame('Russia', $model->getPropertyValue('name'));
    }

    public function testToArray(): void
    {
        $address = new Address(new Country());
        $model = new Profile($address);

        $model->setPropertiesValues(
            [
                'bio' => 'bio',
                'publicEmailPersonal' => 'admin@example.com',
            ],
        );

        $this->assertSame(
            [
                'bio' => 'bio',
                'publicEmailPersonal' => 'admin@example.com',
                'address' => $address,
            ],
            $model->toArray(exceptPropierties: ['pathAvatar'])
        );
    }

    public function testToArrayWithSnakeCase(): void
    {
        $address = new Address(new Country());
        $model = new Profile($address);

        $model->setPropertiesValues(
            [
                'bio' => 'bio',
                'publicEmailPersonal' => 'admin@example.com',
            ],
        );

        $this->assertSame(
            [
                'bio' => 'bio',
                'public_email_personal' => 'admin@example.com',
                'address' => $address,
            ],
            $model->toArray(true, ['pathAvatar'])
        );
    }
}
