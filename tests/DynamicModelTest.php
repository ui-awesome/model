<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\Tests\Support\Model\{Dynamic, DynamicNested};

final class DynamicModelTest extends TestCase
{
    public function testAddProperty(): void
    {
        $model = new Dynamic();

        $model->addProperty('name', 'string');
        $model->addProperty('age', 'int');
        $model->addProperty('email', 'string');

        self::assertSame(
            [
                'name',
                'age',
                'email',
            ],
            $model->getProperties(),
        );
        self::assertSame(
            [
                'name' => 'string',
                'age' => 'int',
                'email' => 'string',
            ],
            $model->getPropertyTypes(),
        );
    }

    public function testAddPropertyWithLoadData(): void
    {
        $model = new Dynamic();

        $model->addProperty('name', 'string');
        $model->addProperty('age', 'int');
        $model->addProperty('email', 'string');

        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'email' => 'test@example.com',
        ];

        $model->load($data, 'Dynamic');

        self::assertSame('John Doe', $model->getPropertyValue('name'));
        self::assertSame(30, $model->getPropertyValue('age'));
        self::assertSame('test@example.com', $model->getPropertyValue('email'));
    }

    public function testAddPropertyWithLoadDataAndTimestamp(): void
    {
        $model = new Dynamic();

        $model->addProperty('name', 'string');
        $model->addProperty('age', 'int');
        $model->addProperty('email', 'string');
        $model->addProperty('created_at', 'timestamp');

        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'email' => 'test@example.com',
        ];

        $model->load($data, 'Dynamic');

        self::assertSame('John Doe', $model->getPropertyValue('name'));
        self::assertSame(30, $model->getPropertyValue('age'));
        self::assertSame('test@example.com', $model->getPropertyValue('email'));
        self::assertTrue($model->getPropertyValue('created_at') > 0);
    }

    public function testNestedAddProperty(): void
    {
        $model = new Dynamic();

        $model->addProperty('name', 'string');
        $model->addProperty('age', 'int');
        $model->addProperty('email', 'string');
        $model->addProperty('address.city', 'string');
        $model->addProperty('address.state', 'string');
        $model->addProperty('address.zip', 'string');

        self::assertSame(
            [
                'name',
                'age',
                'email',
                'address.city',
                'address.state',
                'address.zip',
            ],
            $model->getProperties(),
        );

        self::assertSame(
            [
                'name' => 'string',
                'age' => 'int',
                'email' => 'string',
                'address.city' => 'string',
                'address.state' => 'string',
                'address.zip' => 'string',
            ],
            $model->getPropertyTypes(),
        );
    }

    public function testNestedAddPropertyWithLoadData(): void
    {
        $modelNested = new Dynamic();

        $modelNested->addProperty('city', 'string');
        $modelNested->addProperty('state', 'string');
        $modelNested->addProperty('zip', 'string');
        $modelNested->addProperty('createdAt', 'timestamp');

        $model = new DynamicNested($modelNested);

        $model->addProperty('name', 'string');
        $model->addProperty('age', 'int');
        $model->addProperty('email', 'string');

        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'email' => 'test@example.com',
            'dynamic.city' => 'New York',
            'dynamic.state' => 'NY',
            'dynamic.zip' => '10001',
        ];

        $model->load($data, 'DynamicNested');

        self::assertSame('John Doe', $model->getPropertyValue('name'));
        self::assertSame(30, $model->getPropertyValue('age'));
        self::assertSame('test@example.com', $model->getPropertyValue('email'));
        self::assertSame('New York', $model->getPropertyValue('dynamic.city'));
        self::assertSame('NY', $model->getPropertyValue('dynamic.state'));
        self::assertSame('10001', $model->getPropertyValue('dynamic.zip'));
        self::assertTrue($model->getPropertyValue('dynamic.createdAt') > 0);
    }

    public function testPropertyDoesNotExist(): void
    {
        $model = new Dynamic();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined property: "UIAwesome\Model\Tests\Support\Model\Dynamic::property".');

        $model->getPropertyValue('property.name');
    }
}
