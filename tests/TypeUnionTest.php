<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use stdClass;
use UIAwesome\Model\Tests\Support\Model\UnionType;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class TypeUnionTest extends \PHPUnit\Framework\TestCase
{
    public function testException(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            'Cannot assign float to property UIAwesome\Model\Tests\Support\Model\UnionType::$union of type object|string|int|bool|null'
        );

        $model = new UnionType();
        $model->setPropertyValue('union', 1.1);
    }

    public function testGetPropertiesType(): void
    {
        $model = new UnionType();

        $this->assertSame(
            ['union' => ['object', 'string', 'int', 'bool', 'null']],
            $model->getPropertiesTypes()
        );
    }

    public function testIsPropertyType(): void
    {
        $model = new UnionType();

        $this->assertFalse($model->isPropertyType('union', 'datetime'));
        $this->assertTrue($model->isPropertyType('union', 'object'));
        $this->assertTrue($model->isPropertyType('union', 'string'));
        $this->assertTrue($model->isPropertyType('union', 'int'));
        $this->assertTrue($model->isPropertyType('union', 'bool'));
        $this->assertTrue($model->isPropertyType('union', 'null'));
    }

    public function testPhpTypeCast(): void
    {
        $model = new UnionType();
        $object = new stdClass();

        $model->setPropertyValue('union', 1);

        $this->assertSame(1, $model->getPropertyValue('union'));

        $model->setPropertyValue('union', '1');

        $this->assertSame('1', $model->getPropertyValue('union'));

        $model->setPropertyValue('union', true);

        $this->assertSame(true, $model->getPropertyValue('union'));

        $model->setPropertyValue('union', $object);

        $this->assertSame($object, $model->getPropertyValue('union'));

        $model->setPropertyValue('union', null);

        $this->assertSame(null, $model->getPropertyValue('union'));
    }
}
