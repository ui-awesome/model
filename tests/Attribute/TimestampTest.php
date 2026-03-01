<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Attribute;

use DateTime;
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\Tests\Support\Model\Attributes;

/**
 * Unit tests for timestamp behavior driven by {@see \UIAwesome\Model\Attribute\Timestamp}.
 *
 * Test coverage.
 * - Initializes timestamp properties during bulk assignment and scoped load operations.
 * - Keeps initialized timestamp values stable after first assignment.
 * - Exposes timestamp values as positive integers convertible to `DateTime`.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class TimestampTest extends TestCase
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
            'timestamp',
            $model->getPropertyTypes()['createdAt'] ?? null,
            'Should collect createdAt as a timestamp property type.',
        );
        self::assertSame(
            'timestamp',
            $model->getPropertyTypes()['updatedAt'] ?? null,
            'Should collect updatedAt as a timestamp property type.',
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

    public function testLoadInitializesTimestampPropertiesWhenPayloadContainsMultipleEntries(): void
    {
        $model = new Attributes();

        self::assertTrue(
            $model->load([
                'Attributes' => [
                    'name' => 'samdark',
                    'createdAt' => 0,
                ],
            ]),
            'Should report successful loading when scoped payload contains multiple entries.',
        );

        self::assertGreaterThan(
            0,
            $model->getPropertyValue('createdAt'),
            'Should initialize createdAt timestamp when payload provides zero.',
        );
        self::assertGreaterThan(
            0,
            $model->getPropertyValue('updatedAt'),
            'Should initialize updatedAt timestamp when loading related payload fields.',
        );
    }
}
