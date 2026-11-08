<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use PHPUnit\Framework\Attributes\{Group, RunTestsInSeparateProcesses};
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\BaseModel;

/**
 * Compatibility tests for existing untyped and restricted-visibility clone hooks.
 */
#[Group('model')]
#[RunTestsInSeparateProcesses]
final class CloneHookCompatibilityTest extends TestCase
{
    public function testCloneWithPrivateHook(): void
    {
        $original = new class extends BaseModel {
            public string $name = 'Original';

            public function duplicate(): self
            {
                return clone $this;
            }

            private function __clone(): void
            {
                $this->name = 'Copy';
            }
        };

        $original->getTypes();

        $copy = $original->duplicate();

        self::assertSame(
            'Copy',
            $copy->getValue('name'),
            'A private clone hook must remain loadable and callable internally.',
        );
        self::assertSame(
            'Original',
            $original->name,
            'Private-hook clone reads must not alter original state.',
        );
    }

    public function testCloneWithProtectedHook(): void
    {
        $original = new class extends BaseModel {
            public string $name = 'Original';

            public function duplicate(): self
            {
                return clone $this;
            }

            protected function __clone(): void
            {
                $this->name = 'Copy';
            }
        };

        $original->getTypes();

        $copy = $original->duplicate();

        self::assertSame(
            'Copy',
            $copy->getValue('name'),
            'A protected clone hook must remain loadable and callable internally.',
        );
        self::assertSame(
            'Original',
            $original->name,
            'Protected-hook clone reads must not alter original state.',
        );
    }

    public function testCloneWithUntypedHook(): void
    {
        $original = new class extends BaseModel {
            public string $name = 'Original';

            /**
             * Intentionally retains a legacy hook without a native return type.
             */
            public function __clone()
            {
                $this->name = 'Copy';
            }
        };

        $original->getTypes();

        $copy = clone $original;

        self::assertSame(
            'Copy',
            $copy->getValue('name'),
            'An untyped clone hook must remain loadable and run normally.',
        );
        self::assertSame(
            'Original',
            $original->name,
            'Untyped-hook clone reads must not alter original state.',
        );
    }
}
