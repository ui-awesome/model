<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Attribute;

use DateTime;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\Attribute\Timestamp;
use UIAwesome\Model\BaseModel;
use UIAwesome\Model\Tests\Support\Model\Attributes;

/**
 * Unit tests for timestamp behavior driven by {@see \UIAwesome\Model\Attribute\Timestamp}.
 */
#[Group('attribute')]
final class TimestampTest extends TestCase
{
    public function testDoNotInitializeTimestampPropertiesWhenNoStringKeyedValuesAreAssigned(): void
    {
        $model = new Attributes();

        $model->setValues([]);

        self::assertSame(
            0,
            $model->getValue('createdAt'),
            'Empty assignment must leave `createdAt` untouched.',
        );
        self::assertSame(
            0,
            $model->getValue('updatedAt'),
            'Empty assignment must leave `updatedAt` untouched.',
        );

        $model->setValues([0 => 'positional']);

        self::assertSame(
            0,
            $model->getValue('createdAt'),
            'Non-string keys must not trigger timestamp initialization.',
        );
    }

    public function testDoNotOverwriteNonZeroTimestampPropertyDuringInitialization(): void
    {
        $model = new class extends BaseModel {
            #[Timestamp]
            public int $createdAt = 12345;
        };

        $model->setValues(['createdAt' => 67890]);

        self::assertSame(
            67890,
            $model->getValue('createdAt'),
            'Non-zero timestamp value must be preserved, not replaced by `time()`.',
        );
    }

    public function testInitializeTimestampPropertiesDuringBulkSetValues(): void
    {
        $model = new Attributes();

        $model->setValues(['name' => 'samdark']);

        self::assertGreaterThan(
            0,
            $model->getValue('createdAt'),
            'Should initialize createdAt after bulk property assignment.',
        );
        self::assertGreaterThan(
            0,
            $model->getValue('updatedAt'),
            'Should initialize updatedAt after bulk property assignment.',
        );
    }

    public function testInitializeTimestampPropertiesDuringSingleValueAssignment(): void
    {
        $model = new Attributes();

        $model->setValue('name', 'samdark');

        self::assertGreaterThan(
            0,
            $model->getValue('createdAt'),
            'Should initialize createdAt after assigning a single property.',
        );
        self::assertGreaterThan(
            0,
            $model->getValue('updatedAt'),
            'Should initialize updatedAt after assigning a single property.',
        );
    }

    public function testLoadAttributesAndInitializeTimestampProperties(): void
    {
        $model = new Attributes();

        self::assertSame(
            'timestamp',
            $model->getTypes()['createdAt'] ?? null,
            'Should collect createdAt as a timestamp property type.',
        );
        self::assertSame(
            'timestamp',
            $model->getTypes()['updatedAt'] ?? null,
            'Should collect updatedAt as a timestamp property type.',
        );

        self::assertTrue(
            $model->load(['Attributes' => ['name' => 'samdark']]),
            'Should load data for an attribute-driven model using its scope.',
        );

        $createdAtTimestamp = $model->getValue('createdAt');
        $updatedAtTimestamp = $model->getValue('updatedAt');

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
            $model->getValue('createdAt'),
            'Should keep createdAt stable after initial assignment.',
        );
        self::assertSame(
            $updatedAtTimestamp,
            $model->getValue('updatedAt'),
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
            $model->getValue('createdAt'),
            'Should initialize createdAt timestamp when payload provides zero.',
        );
        self::assertGreaterThan(
            0,
            $model->getValue('updatedAt'),
            'Should initialize updatedAt timestamp when loading related payload fields.',
        );
    }
}
