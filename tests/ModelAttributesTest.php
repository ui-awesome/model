<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use DateTime;
use UIAwesome\Model\Tests\Support\Model\Attributes;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ModelAttributesTest extends \PHPUnit\Framework\TestCase
{
    public function testAttributes(): void
    {
        $model = new Attributes();

        $this->assertSame(
            ['name' => 'string', 'createdAt' => 'timestamp', 'updatedAt' => 'timestamp'],
            $model->getPropertiesTypes()
        );
        $this->assertTrue($model->load(['Attributes' => ['name' => 'samdark']]));
        $this->assertTrue($model->getPropertyValue('createdAt') > 0);
        $this->assertTrue($model->getPropertyValue('updatedAt') > 0);

        $createdAt = new DateTime();
        $createdAt->setTimestamp($model->getPropertyValue('createdAt'));

        $this->assertInstanceOf(DateTime::class, $createdAt);

        $updatedAt = new DateTime();
        $updatedAt->setTimestamp($model->getPropertyValue('updatedAt'));

        $this->assertInstanceOf(DateTime::class, $updatedAt);
    }
}
