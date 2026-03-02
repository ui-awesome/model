<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\Exception\Message;
use UIAwesome\Model\Tests\Support\Model\{Dynamic, DynamicNested};

/**
 * Unit tests for dynamic runtime property registration, loading, and nested dynamic access.
 *
 * Test coverage.
 * - Adds dynamic properties and returns synchronized property names and type metadata.
 * - Initializes timestamp dynamic properties during model loading.
 * - Loads values into dynamic models for flat and nested dynamic paths.
 * - Throws invalid argument exceptions when reading unknown dynamic property paths.
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class DynamicModelTest extends TestCase
{
    public function testAddDynamicPropertiesAndReturnRegisteredMetadata(): void
    {
        $model = new Dynamic();

        $model->add('name', 'string');
        $model->add('age', 'int');
        $model->add('email', 'string');

        self::assertSame(
            [
                'name',
                'age',
                'email',
            ],
            $model->getNames(),
            'Should return registered dynamic property names in insertion order.',
        );
        self::assertSame(
            [
                'name' => 'string',
                'age' => 'int',
                'email' => 'string',
            ],
            $model->getTypes(),
            'Should return registered dynamic property types keyed by property name.',
        );
    }

    public function testAddNestedDynamicPropertyPaths(): void
    {
        $model = new Dynamic();

        $model->add('name', 'string');
        $model->add('age', 'int');
        $model->add('email', 'string');
        $model->add('address.city', 'string');
        $model->add('address.state', 'string');
        $model->add('address.zip', 'string');

        self::assertSame(
            [
                'name',
                'age',
                'email',
                'address.city',
                'address.state',
                'address.zip',
            ],
            $model->getNames(),
            'Should return nested dynamic property paths in insertion order.',
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
            $model->getTypes(),
            'Should return types for nested dynamic property paths.',
        );
    }

    public function testInitializeTimestampWhenLoadingDynamicModelData(): void
    {
        $model = new Dynamic();

        $model->add('name', 'string');
        $model->add('age', 'int');
        $model->add('email', 'string');
        $model->add('created_at', 'timestamp');

        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'email' => 'test@example.com',
        ];

        $model->load($data, 'Dynamic');

        self::assertSame(
            'John Doe',
            $model->getValue('name'),
            'Should keep loaded string values for dynamic properties.',
        );
        self::assertSame(
            30,
            $model->getValue('age'),
            'Should keep loaded integer values for dynamic properties.',
        );
        self::assertSame(
            'test@example.com',
            $model->getValue('email'),
            'Should keep loaded email values for dynamic properties.',
        );
        self::assertTrue(
            $model->getValue('created_at') > 0,
            'Should auto-initialize timestamp properties with a positive integer value.',
        );
    }

    public function testLoadDataIntoNestedDynamicModelProperties(): void
    {
        $modelNested = new Dynamic();

        $modelNested->add('city', 'string');
        $modelNested->add('state', 'string');
        $modelNested->add('zip', 'string');
        $modelNested->add('createdAt', 'timestamp');

        $model = new DynamicNested($modelNested);

        $model->add('name', 'string');
        $model->add('age', 'int');
        $model->add('email', 'string');

        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'email' => 'test@example.com',
            'dynamic.city' => 'New York',
            'dynamic.state' => 'NY',
            'dynamic.zip' => '10001',
        ];

        $model->load($data, 'DynamicNested');

        self::assertSame(
            'John Doe',
            $model->getValue('name'),
            'Should load top-level dynamic name values.',
        );
        self::assertSame(
            30,
            $model->getValue('age'),
            'Should load top-level dynamic age values.',
        );
        self::assertSame(
            'test@example.com',
            $model->getValue('email'),
            'Should load top-level dynamic email values.',
        );
        self::assertSame(
            'New York',
            $model->getValue('dynamic.city'),
            'Should load nested city values.',
        );
        self::assertSame(
            'NY',
            $model->getValue('dynamic.state'),
            'Should load nested state values.',
        );
        self::assertSame(
            '10001',
            $model->getValue('dynamic.zip'),
            'Should load nested ZIP values.',
        );
        self::assertTrue(
            $model->getValue('dynamic.createdAt') > 0,
            'Should initialize nested timestamp properties with a positive integer value.',
        );
    }

    public function testLoadDataIntoRegisteredDynamicProperties(): void
    {
        $model = new Dynamic();

        $model->add('name', 'string');
        $model->add('age', 'int');
        $model->add('email', 'string');

        $data = [
            'name' => 'John Doe',
            'age' => 30,
            'email' => 'test@example.com',
        ];

        $model->load($data, 'Dynamic');

        self::assertSame(
            'John Doe',
            $model->getValue('name'),
            'Should load the dynamic name property value.',
        );
        self::assertSame(
            30,
            $model->getValue('age'),
            'Should load the dynamic age property value.',
        );
        self::assertSame(
            'test@example.com',
            $model->getValue('email'),
            'Should load the dynamic email property value.',
        );
    }

    public function testThrowInvalidArgumentExceptionWhenReadingUnknownDynamicPropertyPath(): void
    {
        $model = new Dynamic();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::UNDEFINED_PROPERTY_WITH_CLASS->getMessage(
                Dynamic::class,
                'property',
            ),
        );

        $model->getValue('property.name');
    }
}
