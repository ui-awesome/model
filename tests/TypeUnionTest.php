<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;
use UIAwesome\Model\Tests\Support\Model\UnionType;

final class TypeUnionTest extends TestCase
{
    public function testException(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            'Cannot assign float to property UIAwesome\Model\Tests\Support\Model\UnionType::$union of type object|string|int|bool|null',
        );

        $model = new UnionType();
        $model->setPropertyValue('union', 1.1);
    }

    public function testGetPropertiesType(): void
    {
        $model = new UnionType();

        self::assertSame(
            ['union' => ['object', 'string', 'int', 'bool', 'null']],
            $model->getPropertyTypes(),
        );
    }

    public function testisPropertyType(): void
    {
        $model = new UnionType();

        self::assertFalse($model->isPropertyType('union', 'datetime'));
        self::assertTrue($model->isPropertyType('union', 'object'));
        self::assertTrue($model->isPropertyType('union', 'string'));
        self::assertTrue($model->isPropertyType('union', 'int'));
        self::assertTrue($model->isPropertyType('union', 'bool'));
        self::assertTrue($model->isPropertyType('union', 'null'));
    }

    public function testPhpTypeCast(): void
    {
        $model = new UnionType();
        $object = new stdClass();

        $model->setPropertyValue('union', 1);

        self::assertSame(1, $model->getPropertyValue('union'));

        $model->setPropertyValue('union', '1');

        self::assertSame('1', $model->getPropertyValue('union'));

        $model->setPropertyValue('union', true);

        self::assertSame(true, $model->getPropertyValue('union'));

        $model->setPropertyValue('union', $object);

        self::assertSame($object, $model->getPropertyValue('union'));

        $model->setPropertyValue('union', null);

        self::assertSame(null, $model->getPropertyValue('union'));
    }
}
