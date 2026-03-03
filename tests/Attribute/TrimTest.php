<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use UIAwesome\Model\Attribute\DoNotCollect;
use UIAwesome\Model\Attribute\Trim;
use UIAwesome\Model\BaseModel;
use UIAwesome\Model\Tests\Support\Model\{TrimAddress, TrimContainer, TrimProfile};

/**
 * Unit tests for input normalization using {@see \UIAwesome\Model\Attribute\Trim}.
 *
 * Test coverage.
 * - Applies trim behavior on nested property writes.
 * - Leaves non-string values unchanged.
 * - Preserves values for properties without trim metadata.
 * - Trims string values during `setValue()`, `setValues()`, and `load()`.
 * - Works with explicit input mapping through `MapFrom`.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class TrimTest extends TestCase
{
    public function testDoNotTrimPropertiesWithoutAttribute(): void
    {
        $model = new TrimProfile();

        $model->setValue('rawName', '  Keep Spaces  ');

        self::assertSame(
            '  Keep Spaces  ',
            $model->getValue('rawName'),
            'Should keep original spacing for properties without Trim.',
        );
    }

    public function testKeepNullValueOnNullablePropertyWithoutTrimCastSideEffects(): void
    {
        $model = new TrimProfile();

        $model->setValue('nickname', null);

        self::assertNull(
            $model->getValue('nickname'),
            'Should preserve null values and avoid applying trim to non-string inputs.',
        );
    }

    public function testLoadTrimsScopedPayloadValues(): void
    {
        $model = new TrimProfile();

        self::assertTrue(
            $model->load([
                'TrimProfile' => [
                    'name' => '  Ada Lovelace  ',
                ],
            ]),
            'Should report successful load for scoped payloads with trim-enabled properties.',
        );

        self::assertSame(
            'Ada Lovelace',
            $model->getValue('name'),
            'Should trim leading and trailing spaces during load().',
        );
    }

    public function testSetValuesTrimsMappedInputKeys(): void
    {
        $model = new TrimProfile();

        $model->setValues([
            'display-name' => '  Ada  ',
        ]);

        self::assertSame(
            'Ada',
            $model->getValue('displayName'),
            'Should trim mapped values resolved by MapFrom.',
        );
    }

    public function testSetValueTrimsStringInput(): void
    {
        $model = new TrimProfile();

        $model->setValue('name', '  Grace Hopper  ');

        self::assertSame(
            'Grace Hopper',
            $model->getValue('name'),
            'Should trim string input when assigning a single property.',
        );
    }

    public function testTrimAppliesToNestedPropertyAssignments(): void
    {
        $model = new TrimContainer(new TrimAddress());

        $model->setValue('address.city', '  Madrid  ');

        self::assertSame(
            'Madrid',
            $model->getValue('address.city'),
            'Should trim values when writing nested properties through dot notation.',
        );
    }

    public function testTrimCollectionContinuesAfterDoNotCollectProperty(): void
    {
        $model = new class extends BaseModel {
            #[DoNotCollect]
            public string $ignored = '';

            #[Trim]
            public string $name = '';
        };

        $model->setValue('name', '  Ada  ');

        self::assertSame(
            'Ada',
            $model->getValue('name'),
            'Should continue scanning trim metadata after DoNotCollect properties.',
        );
    }

    public function testTrimMetadataIgnoresStaticProperties(): void
    {
        $model = new class extends BaseModel {
            #[Trim]
            public static string $tag = '';

            public string $name = '';
        };

        $model->add('tag', 'string');
        $model->setValue('tag', '  keep spaces  ');

        self::assertSame(
            '  keep spaces  ',
            $model->getValue('tag'),
            'Should ignore trim metadata declared on static properties.',
        );
    }
}
