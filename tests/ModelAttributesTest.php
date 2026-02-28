<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\Tests\Support\Model\Attributes;

/**
 * Unit tests for attribute-driven property type metadata and timestamp initialization behavior.
 *
 * Test coverage.
 * - Loads attribute-annotated models and verifies timestamp fields are initialized with positive integer values.
 * - Returns property type metadata inferred from model attributes.
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class ModelAttributesTest extends TestCase
{
    public function testInitializeTimestampPropertiesDuringBulkSetProperties(): void
    {
        $model = new Attributes();

        $model->setProperties(['name' => 'samdark']);

        self::assertGreaterThan(
            0,
            $model->getPropertyValue('createdAt'),
            'Should initialize createdAt after bulk property assignment.',
        );
        self::assertGreaterThan(
            0,
            $model->getPropertyValue('updatedAt'),
            'Should initialize updatedAt after bulk property assignment.',
        );
    }

    public function testLoadAttributesAndInitializeTimestampProperties(): void
    {
        $model = new Attributes();

        self::assertSame(
            [
                'createdAt' => 'timestamp',
                'name' => 'string',
                'updatedAt' => 'timestamp',
            ],
            $model->getPropertyTypes(),
            'Should return property types inferred from model attributes.',
        );

        self::assertTrue(
            $model->load(['Attributes' => ['name' => 'samdark']]),
            'Should load data for an attribute-driven model using its scope.',
        );

        $createdAtTimestamp = $model->getPropertyValue('createdAt');
        $updatedAtTimestamp = $model->getPropertyValue('updatedAt');

        self::assertIsInt(
            $createdAtTimestamp,
            'Should set createdAt as an integer timestamp.',
        );
        self::assertIsInt(
            $updatedAtTimestamp,
            'Should set updatedAt as an integer timestamp.',
        );
        self::assertGreaterThan(
            0,
            $createdAtTimestamp,
            'Should set createdAt to a positive timestamp value.',
        );
        self::assertGreaterThan(
            0,
            $updatedAtTimestamp,
            'Should set updatedAt to a positive timestamp value.',
        );
        self::assertSame(
            $createdAtTimestamp,
            $model->getPropertyValue('createdAt'),
            'Should keep createdAt stable after initial assignment.',
        );
        self::assertSame(
            $updatedAtTimestamp,
            $model->getPropertyValue('updatedAt'),
            'Should keep updatedAt stable after initial assignment.',
        );

        $createdAt = new DateTime();

        $createdAt->setTimestamp($createdAtTimestamp);

        self::assertInstanceOf(
            DateTime::class,
            $createdAt,
            'Should allow converting createdAt timestamp to DateTime.',
        );

        $updatedAt = new DateTime();

        $updatedAt->setTimestamp($updatedAtTimestamp);

        self::assertInstanceOf(
            DateTime::class,
            $updatedAt,
            'Should allow converting updatedAt timestamp to DateTime.',
        );
    }
}
