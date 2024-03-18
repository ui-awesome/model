<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use UIAwesome\Model\Tests\Support\Model\{Dinamic, DinamicNested};

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class DinamicModelTest extends \PHPUnit\Framework\TestCase
{
    public function testAddProperty(): void
    {
        $model = new Dinamic();

        $model->addProperty('name', 'string');
        $model->addProperty('age', 'int');
        $model->addProperty('email', 'string');

        $this->assertSame(
            [
                'name',
                'age',
                'email',
            ],
            $model->getProperties()
        );
        $this->assertSame(
            [
                'name' => 'string',
                'age' => 'int',
                'email' => 'string',
            ],
            $model->getPropertiesTypes()
        );
    }

    public function testAddPropertyWithLoadData(): void
    {
        $model = new Dinamic();

        $model->addProperty('name', 'string');
        $model->addProperty('age', 'int');
        $model->addProperty('email', 'string');

        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'email' => 'test@example.com',
        ];

        $model->load($data, 'Dinamic');

        $this->assertSame('John Doe', $model->getPropertyValue('name'));
        $this->assertSame(30, $model->getPropertyValue('age'));
        $this->assertSame('test@example.com', $model->getPropertyValue('email'));
    }

    public function testAddPropertyWithLoadDataAndTimestamp(): void
    {
        $model = new Dinamic();

        $model->addProperty('name', 'string');
        $model->addProperty('age', 'int');
        $model->addProperty('email', 'string');
        $model->addProperty('created_at', 'timestamp');

        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'email' => 'test@example.com',
        ];

        $model->load($data, 'Dinamic');

        $this->assertSame('John Doe', $model->getPropertyValue('name'));
        $this->assertSame(30, $model->getPropertyValue('age'));
        $this->assertSame('test@example.com', $model->getPropertyValue('email'));
        $this->assertTrue($model->getPropertyValue('created_at') > 0);
    }

    public function testPropertyDoesNotExist(): void
    {
        $model = new Dinamic();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined property: "UIAwesome\Model\Tests\Support\Model\Dinamic::property".');

        $model->getPropertyValue('property.name');
    }

    public function testNestedAddProperty(): void
    {
        $model = new Dinamic();

        $model->addProperty('name', 'string');
        $model->addProperty('age', 'int');
        $model->addProperty('email', 'string');
        $model->addProperty('address.city', 'string');
        $model->addProperty('address.state', 'string');
        $model->addProperty('address.zip', 'string');

        $this->assertSame(
            [
                'name',
                'age',
                'email',
                'address.city',
                'address.state',
                'address.zip',
            ],
            $model->getProperties()
        );

        $this->assertSame(
            [
                'name' => 'string',
                'age' => 'int',
                'email' => 'string',
                'address.city' => 'string',
                'address.state' => 'string',
                'address.zip' => 'string',
            ],
            $model->getPropertiesTypes()
        );
    }

    public function testNestedAddPropertyWithLoadData(): void
    {
        $modelNested = new Dinamic();

        $modelNested->addProperty('city', 'string');
        $modelNested->addProperty('state', 'string');
        $modelNested->addProperty('zip', 'string');
        $modelNested->addProperty('createdAt', 'timestamp');

        $model = new DinamicNested($modelNested);

        $model->addProperty('name', 'string');
        $model->addProperty('age', 'int');
        $model->addProperty('email', 'string');

        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'email' => 'test@example.com',
            'dinamic.city' => 'New York',
            'dinamic.state' => 'NY',
            'dinamic.zip' => '10001',
        ];

        $model->load($data, 'DinamicNested');

        $this->assertSame('John Doe', $model->getPropertyValue('name'));
        $this->assertSame(30, $model->getPropertyValue('age'));
        $this->assertSame('test@example.com', $model->getPropertyValue('email'));
        $this->assertSame('New York', $model->getPropertyValue('dinamic.city'));
        $this->assertSame('NY', $model->getPropertyValue('dinamic.state'));
        $this->assertSame('10001', $model->getPropertyValue('dinamic.zip'));
        $this->assertTrue($model->getPropertyValue('dinamic.createdAt') > 0);
    }
}
