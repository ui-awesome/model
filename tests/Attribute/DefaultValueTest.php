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

        $model->setValue('bio', '   ');

        self::assertSame(
            'Unknown',
            $model->getValue('bio'),
            'Should trim whitespace and apply default when the resulting value is empty.',
        );
    }

    public function testApplyDefaultBeforeCastPipeline(): void
    {
        $model = new DefaultValuePayload();

        $model->setValue('tags', null);

        self::assertSame(
            ['php', 'model'],
            $model->getValue('tags'),
            'Should apply default string and then cast it to an array using the configured Cast attribute.',
        );
    }

    public function testApplyDefaultValueWhenInputIsEmptyString(): void
    {
        $model = new DefaultValuePayload();

        $model->setValue('status', '');

        self::assertSame(
            'draft',
            $model->getValue('status'),
            'Should apply configured default value when assigned input is an empty string.',
        );
    }

    public function testApplyDefaultValueWhenInputIsNull(): void
    {
        $model = new DefaultValuePayload();

        $model->setValue('displayName', null);

        self::assertSame(
            'Guest',
            $model->getValue('displayName'),
            'Should apply configured default value when assigned input is null.',
        );
    }

    public function testApplyDefaultWithMappedInputKeyDuringSetProperties(): void
    {
        $model = new DefaultValuePayload();

        $model->setValues(['user-locale' => '']);

        self::assertSame(
            'en_US',
            $model->getValue('locale'),
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

        $model->setValue('name', null);

        self::assertSame(
            'guest',
            $model->getValue('name'),
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

        $model->setValue('name', null);

        self::assertSame(
            'ok',
            $model->getValue('name'),
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

        $model->setValue('name', null);

        self::assertSame(
            'kept',
            $model->getValue('name'),
            'Should ignore DefaultValue metadata declared on DoNotCollect properties.',
        );
    }

    public function testIgnoreParentDoNotCollectDefaultValueWhenChildReusesPropertyName(): void
    {
        $model = new DefaultValueChild();

        $model->setValue('status', null);

        self::assertNull(
            $model->getValue('status'),
            'Should ignore parent DoNotCollect DefaultValue metadata for child properties with the same name.',
        );
    }

    public function testKeepExplicitValueWhenInputIsNotEmpty(): void
    {
        $model = new DefaultValuePayload();

        $model->setValue('displayName', 'Ada');

        self::assertSame(
            'Ada',
            $model->getValue('displayName'),
            'Should keep explicit non-empty values instead of replacing them with defaults.',
        );
    }
}
