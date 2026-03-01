<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Attribute;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\{DoNotCollect, MapFrom};
use UIAwesome\Model\Exception\Message;
use UIAwesome\Model\Tests\Support\Model\{MapFromDuplicate, MapFromPayload};

/**
 * Unit tests for explicit input-key mapping with {@see \UIAwesome\Model\Attribute\MapFrom}.
 *
 * Test coverage.
 * - Applies mapping consistently across `setProperties()` and `load()`.
 * - Keeps `exceptProperties` behavior based on resolved camelCase property names.
 * - Maps non-snake_case payload keys to model properties via `MapFrom`.
 * - Preserves snake_case to camelCase fallback for properties without `MapFrom`.
 * - Throws explicit errors for duplicate `MapFrom` key declarations.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class MapFromAttributeTest extends TestCase
{
    public function testCollectMappedKeysAfterDoNotCollectPropertyDeclaration(): void
    {
        $model = new class extends AbstractModel {
            #[DoNotCollect]
            public string $ignored = '';

            #[MapFrom('external-key')]
            public string $name = '';
        };

        $model->setProperties(['external-key' => 'Lin']);

        self::assertSame(
            'Lin',
            $model->getPropertyValue('name'),
            'Should continue scanning properties after DoNotCollect entries to gather later MapFrom keys.',
        );
    }

    public function testIgnoreMapFromDeclaredOnDoNotCollectPropertyWhenCheckingDuplicates(): void
    {
        $model = new class extends AbstractModel {
            #[DoNotCollect]
            #[MapFrom('same-key')]
            public string $ignored = '';

            #[MapFrom('same-key')]
            public string $name = '';
        };

        $model->setProperties(['same-key' => 'Ada']);

        self::assertSame(
            'Ada',
            $model->getPropertyValue('name'),
            'Should ignore MapFrom metadata on DoNotCollect properties when resolving keys.',
        );
    }

    public function testLoadWithMapFromKeysAndSnakeCaseFallback(): void
    {
        $model = new MapFromPayload();

        self::assertTrue(
            $model->load(
                [
                    'MapFromPayload' => [
                        '@context' => 'https://schema.org',
                        'user-email-address' => 'admin@example.com',
                        'public_email_personal' => 'public@example.com',
                    ],
                ],
            ),
            'Should load values from explicit MapFrom keys and snake_case fallback keys.',
        );
        self::assertSame(
            'https://schema.org',
            $model->getPropertyValue('context'),
            'Should map @context payload key to the context property.',
        );
        self::assertSame(
            'admin@example.com',
            $model->getPropertyValue('userEmailAddress'),
            'Should map non-snake_case payload key to property through MapFrom.',
        );
        self::assertSame(
            'public@example.com',
            $model->getPropertyValue('publicEmailPersonal'),
            'Should keep snake_case to camelCase fallback for properties without MapFrom.',
        );
    }

    public function testSetPropertiesAllowsDirectPropertyNameOnMappedField(): void
    {
        $model = new MapFromPayload();

        $model->setProperties(['userEmailAddress' => 'direct@example.com']);

        self::assertSame(
            'direct@example.com',
            $model->getPropertyValue('userEmailAddress'),
            'Should still allow direct camelCase assignment for mapped properties.',
        );
    }

    public function testSetPropertiesWithMapFromKeys(): void
    {
        $model = new MapFromPayload();

        $model->setProperties(
            [
                '@context' => 'https://example.com/context',
                'user-email-address' => 'hello@example.com',
            ],
        );

        self::assertSame(
            'https://example.com/context',
            $model->getPropertyValue('context'),
            'Should resolve explicit input key mapping in setProperties.',
        );
        self::assertSame(
            'hello@example.com',
            $model->getPropertyValue('userEmailAddress'),
            'Should assign mapped values for explicit input keys in setProperties.',
        );
    }

    public function testSkipMappedPropertyWhenExcludedByCamelCaseName(): void
    {
        $model = new MapFromPayload();

        $model->setProperties(
            ['user-email-address' => 'admin@example.com'],
            ['userEmailAddress'],
        );

        self::assertSame(
            '',
            $model->getPropertyValue('userEmailAddress'),
            'Should skip mapped assignments when the resolved property is in exceptProperties.',
        );
    }

    public function testThrowInvalidArgumentExceptionWhenMapFromKeyIsDuplicated(): void
    {
        $model = new MapFromDuplicate();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::DUPLICATE_MAP_FROM_KEY->getMessage(
                'duplicate-key',
                MapFromDuplicate::class,
                'first',
                MapFromDuplicate::class,
                'second',
            ),
        );

        $model->setProperties(['duplicate-key' => 'value']);
    }

    public function testThrowInvalidArgumentExceptionWhenMapFromKeyIsEmpty(): void
    {
        $model = new class extends AbstractModel {
            #[MapFrom('')]
            public string $name = '';
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::MAP_FROM_KEY_EMPTY->getMessage(),
        );

        $model->setProperties(['name' => 'Ada']);
    }

    public function testThrowInvalidArgumentExceptionWhenMapFromKeyIsWhitespaceOnly(): void
    {
        $model = new class extends AbstractModel {
            #[MapFrom('   ')]
            public string $name = '';
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::MAP_FROM_KEY_EMPTY->getMessage(),
        );

        $model->setProperties(['name' => 'Ada']);
    }
}
