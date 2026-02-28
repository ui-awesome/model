<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Provider;

/**
 * Data provider for {@see \UIAwesome\Model\Tests\ModelNestedTest} test cases.
 *
 * Provides representative input/output pairs for nested property paths and expected values.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class ModelNestedProvider
{
    /**
     * @phpstan-return array<string, array{string, string}>
     */
    public static function nestedPropertyValues(): array
    {
        return [
            'user name' => ['name', 'valery'],
            'profile biography' => ['profile.bio', 'senior software engineer'],
            'profile address street' => ['profile.address.street', 'ulitsa 9-ya Voronezhskaya'],
            'profile address city' => ['profile.address.city', 'Voronezh'],
            'profile address country name' => ['profile.address.country.name', 'Russia'],
        ];
    }
}
