<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\Tests\Support\Model\Attributes;

final class ModelAttributesTest extends TestCase
{
    public function testAttributes(): void
    {
        $model = new Attributes();

        self::assertSame(
            [
                'createdAt' => 'timestamp',
                'name' => 'string',
                'updatedAt' => 'timestamp',
            ],
            $model->getPropertyTypes(),
        );

        self::assertTrue($model->load(['Attributes' => ['name' => 'samdark']]));

        $createdAtTimestamp = $model->getPropertyValue('createdAt');
        $updatedAtTimestamp = $model->getPropertyValue('updatedAt');

        self::assertIsInt($createdAtTimestamp);
        self::assertIsInt($updatedAtTimestamp);
        self::assertGreaterThan(0, $createdAtTimestamp);
        self::assertGreaterThan(0, $updatedAtTimestamp);
        self::assertSame($createdAtTimestamp, $model->getPropertyValue('createdAt'));
        self::assertSame($updatedAtTimestamp, $model->getPropertyValue('updatedAt'));

        $createdAt = new DateTime();
        $createdAt->setTimestamp($createdAtTimestamp);

        self::assertInstanceOf(DateTime::class, $createdAt);

        $updatedAt = new DateTime();
        $updatedAt->setTimestamp($updatedAtTimestamp);

        self::assertInstanceOf(DateTime::class, $updatedAt);
    }
}
