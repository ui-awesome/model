<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use UIAwesome\Model\Tests\Support\Model\Attributes;

/**
 * Unit tests for exclusion behavior driven by {@see \UIAwesome\Model\Attribute\DoNotCollect}.
 *
 * Test coverage.
 * - Excludes `DoNotCollect` properties from collected type metadata.
 * - Excludes `DoNotCollect` properties from flattened model property lists.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class DoNotCollectTest extends TestCase
{
    public function testExcludeDoNotCollectPropertyFromCollectedTypes(): void
    {
        $model = new Attributes();

        self::assertArrayNotHasKey(
            'flag',
            $model->getPropertyTypes(),
            'Should omit DoNotCollect properties from collected type metadata.',
        );
    }

    public function testExcludeDoNotCollectPropertyFromFlattenedProperties(): void
    {
        $model = new Attributes();

        self::assertNotContains(
            'flag',
            $model->getProperties(),
            'Should omit DoNotCollect properties from flattened property names.',
        );
    }
}
