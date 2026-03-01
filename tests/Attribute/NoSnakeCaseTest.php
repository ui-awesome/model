<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\{DoNotCollect, NoSnakeCase};
use UIAwesome\Model\Tests\Support\Model\{NoSnakeCaseChild, NoSnakeCasePayload};

/**
 * Unit tests for selective key preservation using {@see \UIAwesome\Model\Attribute\NoSnakeCase}.
 *
 * Test coverage.
 * - Collects and preserves multiple NoSnakeCase properties in the same model.
 * - Continues scanning NoSnakeCase metadata after `DoNotCollect` properties.
 * - Continues scanning NoSnakeCase metadata after static property declarations.
 * - Ignores NoSnakeCase metadata declared on DoNotCollect properties from parent classes.
 * - Keeps default snake_case conversion for non-marked properties.
 * - Preserves marked property names when serializing with `toArray(snakeCase: true)`.
 * - Respects `exceptProperties` while preserving configured keys.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class NoSnakeCaseTest extends TestCase
{
    public function testCollectNoSnakeCaseMetadataAfterDoNotCollectProperty(): void
    {
        $model = new class extends AbstractModel {
            #[DoNotCollect]
            public string $ignored = '';

            #[NoSnakeCase]
            public string $apiVersion = '';
        };

        $model->setPropertyValue('apiVersion', 'v3');

        self::assertSame(
            ['apiVersion' => 'v3'],
            $model->toArray(snakeCase: true),
            'Should continue scanning properties after DoNotCollect entries to gather NoSnakeCase metadata.',
        );
    }

    public function testCollectNoSnakeCaseMetadataAfterStaticPropertyDeclaration(): void
    {
        $model = new class extends AbstractModel {
            public static string $ignored = '';

            #[NoSnakeCase]
            public string $apiVersion = '';
        };

        $model->setPropertyValue('apiVersion', 'v4');

        self::assertSame(
            ['apiVersion' => 'v4'],
            $model->toArray(snakeCase: true),
            'Should continue scanning NoSnakeCase metadata after static properties.',
        );
    }

    public function testExcludePreservedPropertyWhenListedInExceptProperties(): void
    {
        $model = new NoSnakeCasePayload();

        $model->setProperties(
            [
                'apiVersion' => 'v2',
                'publicEmailPersonal' => 'admin@example.com',
            ],
        );

        self::assertSame(
            ['public_email_personal' => 'admin@example.com'],
            $model->toArray(snakeCase: true, exceptProperties: ['apiVersion']),
            'Should still honor exceptProperties for NoSnakeCase-marked properties.',
        );
    }

    public function testIgnoreParentDoNotCollectNoSnakeCaseMetadataWhenChildReusesPropertyName(): void
    {
        $model = new NoSnakeCaseChild();

        $model->setPropertyValue('apiVersion', 'v2');

        self::assertSame(
            ['api_version' => 'v2'],
            $model->toArray(snakeCase: true),
            'Should ignore NoSnakeCase metadata on parent DoNotCollect properties when child properties share the name.',
        );
    }

    public function testPreserveMarkedPropertyNameWhenConvertingToSnakeCaseArray(): void
    {
        $model = new NoSnakeCasePayload();

        $model->setProperties(
            [
                'apiVersion' => 'v1',
                'publicEmailPersonal' => 'admin@example.com',
            ],
        );

        self::assertSame(
            [
                'apiVersion' => 'v1',
                'public_email_personal' => 'admin@example.com',
            ],
            $model->toArray(snakeCase: true),
            'Should preserve NoSnakeCase property keys while converting non-marked properties to snake_case.',
        );
    }

    public function testPreserveMultipleNoSnakeCaseProperties(): void
    {
        $model = new class extends AbstractModel {
            #[NoSnakeCase]
            public string $apiVersion = '';

            #[NoSnakeCase]
            public string $oauthClientId = '';

            public string $publicEmailPersonal = '';
        };

        $model->setProperties(
            [
                'apiVersion' => 'v1',
                'oauthClientId' => 'client-01',
                'publicEmailPersonal' => 'admin@example.com',
            ],
        );

        self::assertSame(
            [
                'apiVersion' => 'v1',
                'oauthClientId' => 'client-01',
                'public_email_personal' => 'admin@example.com',
            ],
            $model->toArray(snakeCase: true),
            'Should preserve all NoSnakeCase-marked keys when multiple mapped properties are present.',
        );
    }
}
