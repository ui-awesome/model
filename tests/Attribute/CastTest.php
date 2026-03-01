<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Attribute;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\{Cast, DoNotCollect};
use UIAwesome\Model\Exception\Message;
use UIAwesome\Model\Tests\Support\Model\CastPayload;

/**
 * Unit tests for custom casting through {@see \UIAwesome\Model\Attribute\Cast}.
 *
 * Test coverage.
 * - Applies cast behavior in `setPropertyValue()`, `setProperties()`, and `load()`.
 * - Casts comma-separated strings to arrays.
 * - Continues scanning cast metadata after static property declarations.
 * - Supports custom caster classes implementing `CastValueInterface`.
 * - Supports mapped keys and trim normalization before cast execution.
 * - Throws explicit exceptions for invalid cast targets and invalid caster classes.
 * - Validates `Cast` attribute configuration for empty/blank target and empty separator.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class CastTest extends TestCase
{
    public function testCastArrayFromCommaSeparatedString(): void
    {
        $model = new CastPayload();

        $model->setPropertyValue('tags', 'tag1,tag2, tag3');

        self::assertSame(
            ['tag1', 'tag2', 'tag3'],
            $model->getPropertyValue('tags'),
            'Should cast comma-separated string into trimmed array items.',
        );
    }

    public function testCastArrayRemovesEmptyItems(): void
    {
        $model = new CastPayload();

        $model->setPropertyValue('tags', 'tag1, ,tag2,,');

        self::assertSame(
            ['tag1', 'tag2'],
            $model->getPropertyValue('tags'),
            'Should remove empty values produced by separator parsing.',
        );
    }

    public function testCastNonStringScalarToArray(): void
    {
        $model = new CastPayload();

        $model->setPropertyValue('tags', 7);

        self::assertSame(
            [7],
            $model->getPropertyValue('tags'),
            'Should cast non-string scalar values to array for array cast target.',
        );
    }

    public function testCastWithCustomCasterClass(): void
    {
        $model = new CastPayload();

        $model->setProperties([
            'keywords' => 'red| green |blue',
        ]);

        self::assertSame(
            ['red', 'green', 'blue'],
            $model->getPropertyValue('keywords'),
            'Should delegate casting to custom caster classes.',
        );
    }

    public function testCastWithMapFromAndTrimDuringLoad(): void
    {
        $model = new CastPayload();

        self::assertTrue(
            $model->load([
                'CastPayload' => [
                    'tag-list' => '  alpha, beta , gamma  ',
                ],
            ]),
            'Should load scoped payloads that include mapped cast keys.',
        );

        self::assertSame(
            ['alpha', 'beta', 'gamma'],
            $model->getPropertyValue('tagList'),
            'Should apply MapFrom, Trim, and Cast pipeline during load.',
        );
    }

    public function testCollectCastMetadataAfterDoNotCollectProperty(): void
    {
        $model = new class extends AbstractModel {
            #[DoNotCollect]
            public string $ignored = '';

            #[Cast('array')]
            public array $tags = [];
        };

        $model->setPropertyValue('tags', 'a,b');

        self::assertSame(
            ['a', 'b'],
            $model->getPropertyValue('tags'),
            'Should continue collecting Cast metadata after DoNotCollect properties.',
        );
    }

    public function testCollectCastMetadataAfterPropertyWithoutCastAttribute(): void
    {
        $model = new class extends AbstractModel {
            public string $name = '';

            #[Cast('array')]
            public array $tags = [];
        };

        $model->setPropertyValue('tags', 'x,y');

        self::assertSame(
            ['x', 'y'],
            $model->getPropertyValue('tags'),
            'Should continue scanning properties when some do not declare Cast.',
        );
    }

    public function testCollectCastMetadataAfterStaticPropertyDeclaration(): void
    {
        $model = new class extends AbstractModel {
            #[Cast('array')]
            public static string $ignored = '';

            #[Cast('array')]
            public array $tags = [];
        };

        $model->setPropertyValue('tags', 'x,y');

        self::assertSame(
            ['x', 'y'],
            $model->getPropertyValue('tags'),
            'Should continue scanning cast metadata after static property declarations.',
        );
    }

    public function testIgnoreCastMetadataOnDoNotCollectProperty(): void
    {
        $model = new class extends AbstractModel {
            #[DoNotCollect]
            #[Cast('')]
            public array $ignored = [];

            public array $tags = [];
        };

        $model->setPropertyValue('tags', ['safe']);

        self::assertSame(
            ['safe'],
            $model->getPropertyValue('tags'),
            'Should not evaluate Cast metadata declared on DoNotCollect properties.',
        );
    }

    public function testKeepArrayInputWithoutAdditionalCasting(): void
    {
        $model = new CastPayload();

        $model->setPropertyValue('tags', ['a', 'b']);

        self::assertSame(
            ['a', 'b'],
            $model->getPropertyValue('tags'),
            'Should keep array inputs unchanged for array cast target.',
        );
    }

    public function testThrowInvalidArgumentExceptionWhenCastSeparatorIsEmpty(): void
    {
        $model = new class extends AbstractModel {
            #[Cast('array', '')]
            public array $tags = [];
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(Message::CAST_SEPARATOR_EMPTY->getMessage());

        $model->setPropertyValue('tags', 'a,b');
    }

    public function testThrowInvalidArgumentExceptionWhenCastTargetClassDoesNotExist(): void
    {
        $model = new class extends AbstractModel {
            #[Cast('App\\NotFound\\Caster')]
            public array $tags = [];
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::INVALID_CAST_TARGET->getMessage('App\\NotFound\\Caster', $model::class, 'tags'),
        );

        $model->setPropertyValue('tags', 'a,b');
    }

    public function testThrowInvalidArgumentExceptionWhenCastTargetClassDoesNotImplementContract(): void
    {
        $model = new class extends AbstractModel {
            #[Cast(stdClass::class)]
            public array $tags = [];
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::INVALID_CAST_CLASS->getMessage(
                stdClass::class,
                $model::class,
                'tags',
                \UIAwesome\Model\CastValueInterface::class,
            ),
        );

        $model->setPropertyValue('tags', 'a,b');
    }

    public function testThrowInvalidArgumentExceptionWhenCastTargetIsBlankSpaces(): void
    {
        $model = new class extends AbstractModel {
            #[Cast('   ')]
            public array $tags = [];
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(Message::CAST_TARGET_EMPTY->getMessage());

        $model->setPropertyValue('tags', 'a,b');
    }

    public function testThrowInvalidArgumentExceptionWhenCastTargetIsEmpty(): void
    {
        $model = new class extends AbstractModel {
            #[Cast('')]
            public array $tags = [];
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(Message::CAST_TARGET_EMPTY->getMessage());

        $model->setPropertyValue('tags', 'a,b');
    }
}
