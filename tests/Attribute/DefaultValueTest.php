<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Attribute;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\Attribute\{DefaultValue, DoNotCollect};
use UIAwesome\Model\BaseModel;
use UIAwesome\Model\Tests\Support\Model\{DefaultValueChild, DefaultValuePayload};

/**
 * Unit tests for runtime fallback assignment using {@see \UIAwesome\Model\Attribute\DefaultValue}.
 */
#[Group('attribute')]
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

    public function testApplyDefaultWithMappedInputKeyDuringSetValues(): void
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
        $model = new class extends BaseModel {
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
        $model = new class extends BaseModel {
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
        $model = new class extends BaseModel {
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
