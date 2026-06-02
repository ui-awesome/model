<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Attribute;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\Tests\Support\Model\Attributes;

/**
 * Unit tests for exclusion behavior driven by {@see \UIAwesome\Model\Attribute\DoNotCollect}.
 */
#[Group('attribute')]
final class DoNotCollectTest extends TestCase
{
    public function testExcludeDoNotCollectPropertyFromCollectedTypes(): void
    {
        $model = new Attributes();

        self::assertArrayNotHasKey(
            'flag',
            $model->getTypes(),
            'Should omit DoNotCollect properties from collected type metadata.',
        );
    }

    public function testExcludeDoNotCollectPropertyFromFlattenedProperties(): void
    {
        $model = new Attributes();

        self::assertNotContains(
            'flag',
            $model->getNames(),
            'Should omit DoNotCollect properties from flattened property names.',
        );
    }
}
