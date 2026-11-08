<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Provider;

/**
 * Collector initialization states for {@see \UIAwesome\Model\Tests\ModelCloneTest}.
 */
final class ModelCloneProvider
{
    /**
     * @phpstan-return array<string, array{bool}>
     */
    public static function collectorStates(): array
    {
        return [
            'before initialization' => [false],
            'after initialization' => [true],
        ];
    }
}
