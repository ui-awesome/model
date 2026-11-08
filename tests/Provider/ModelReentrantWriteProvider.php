<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Provider;

/**
 * Write entry points for {@see \UIAwesome\Model\Tests\ModelReentrantWriteTest}.
 */
final class ModelReentrantWriteProvider
{
    /**
     * @phpstan-return array<string, array{bool}>
     */
    public static function bulkEntryPoints(): array
    {
        return [
            'setValues' => [false],
            'scoped load' => [true],
        ];
    }

    /**
     * @phpstan-return array<string, array{bool}>
     */
    public static function failingEntryPoints(): array
    {
        return [
            'setValue' => [false],
            'setValues' => [true],
        ];
    }
}
