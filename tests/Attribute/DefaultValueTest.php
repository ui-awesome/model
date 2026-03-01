<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\{DefaultValue, DoNotCollect};
use UIAwesome\Model\Tests\Support\Model\{DefaultValueChild, DefaultValuePayload};

/**
 * Unit tests for runtime fallback assignment using {@see \UIAwesome\Model\Attribute\DefaultValue}.
 *
 * Test coverage.
 * - Applies default values when input is `null` or an empty string.
 * - Applies defaults after trim normalization for whitespace-only strings.
 * - Applies defaults before custom casting in the assignment pipeline.
 * - Applies defaults with mapped input keys via `MapFrom`.
 * - Continues collecting `DefaultValue` metadata after non-decorated or ignored properties.
 * - Ignores `DefaultValue` metadata declared on `DoNotCollect` and static properties.
 * - Ignores parent `DoNotCollect` `DefaultValue` metadata when child properties reuse names.
 * - Keeps explicit non-empty values unchanged.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class DefaultValueTest extends TestCase
{
    public function testApplyDefaultAfterTrimForWhitespaceOnlyString(): void
    {
        $model = new DefaultValuePayload();

        $model->setPropertyValue('bio', '   ');

        self::assertSame(
            'Unknown',
            $model->getPropertyValue('bio'),
            'Should trim whitespace and apply default when the resulting value is empty.',
        );
    }

    public function testApplyDefaultBeforeCastPipeline(): void
    {
        $model = new DefaultValuePayload();

        $model->setPropertyValue('tags', null);

        self::assertSame(
            ['php', 'model'],
            $model->getPropertyValue('tags'),
            'Should apply default string and then cast it to an array using the configured Cast attribute.',
        );
    }

    public function testApplyDefaultValueWhenInputIsEmptyString(): void
    {
        $model = new DefaultValuePayload();

        $model->setPropertyValue('status', '');

        self::assertSame(
            'draft',
            $model->getPropertyValue('status'),
            'Should apply configured default value when assigned input is an empty string.',
        );
    }

    public function testApplyDefaultValueWhenInputIsNull(): void
    {
        $model = new DefaultValuePayload();

        $model->setPropertyValue('displayName', null);

        self::assertSame(
            'Guest',
            $model->getPropertyValue('displayName'),
            'Should apply configured default value when assigned input is null.',
        );
    }

    public function testApplyDefaultWithMappedInputKeyDuringSetProperties(): void
    {
        $model = new DefaultValuePayload();

        $model->setProperties(['user-locale' => '']);

        self::assertSame(
            'en_US',
            $model->getPropertyValue('locale'),
            'Should apply defaults after resolving mapped input keys.',
        );
    }

    public function testCollectDefaultValueMetadataAfterPropertyWithoutAttribute(): void
    {
        $model = new class extends AbstractModel {
            public string $title = '';

            #[DefaultValue('guest')]
            public string $name = '';
        };

        $model->setPropertyValue('name', null);

        self::assertSame(
            'guest',
            $model->getPropertyValue('name'),
            'Should continue scanning properties when some entries do not declare DefaultValue.',
        );
    }

    public function testCollectDefaultValueMetadataAfterStaticPropertyDeclaration(): void
    {
        $model = new class extends AbstractModel {
            #[DefaultValue('ignored-static')]
            public static string $ignored = '';

            #[DefaultValue('ok')]
            public string $name = '';
        };

        $model->setPropertyValue('name', null);

        self::assertSame(
            'ok',
            $model->getPropertyValue('name'),
            'Should continue collecting DefaultValue metadata after static properties.',
        );
    }

    public function testIgnoreDefaultValueMetadataOnDoNotCollectProperty(): void
    {
        $model = new class extends AbstractModel {
            #[DoNotCollect]
            #[DefaultValue('ignored')]
            public string $ignored = '';

            #[DefaultValue('kept')]
            public string $name = '';
        };

        $model->setPropertyValue('name', null);

        self::assertSame(
            'kept',
            $model->getPropertyValue('name'),
            'Should ignore DefaultValue metadata declared on DoNotCollect properties.',
        );
    }

    public function testIgnoreParentDoNotCollectDefaultValueWhenChildReusesPropertyName(): void
    {
        $model = new DefaultValueChild();

        $model->setPropertyValue('status', null);

        self::assertNull(
            $model->getPropertyValue('status'),
            'Should ignore parent DoNotCollect DefaultValue metadata for child properties with the same name.',
        );
    }

    public function testKeepExplicitValueWhenInputIsNotEmpty(): void
    {
        $model = new DefaultValuePayload();

        $model->setPropertyValue('displayName', 'Ada');

        self::assertSame(
            'Ada',
            $model->getPropertyValue('displayName'),
            'Should keep explicit non-empty values instead of replacing them with defaults.',
        );
    }
}
