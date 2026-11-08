<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Provider;

use UIAwesome\Model\Tests\Support\Model\{CloneHook, Country};

/**
 * Model and collector initialization states for {@see \UIAwesome\Model\Tests\ModelCloneTest}.
 */
final class ModelCloneProvider
{
    /**
     * @phpstan-return array<string, array{Country|CloneHook, bool}>
     */
    public static function collectorStates(): array
    {
        return [
            'after initialization' => [new Country(), true],
            'before initialization' => [new Country(), false],
            'custom hook after initialization' => [new CloneHook(), true],
            'custom hook before initialization' => [new CloneHook(), false],
        ];
    }
}
