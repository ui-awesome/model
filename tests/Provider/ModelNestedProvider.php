<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Provider;

use stdClass;
use UIAwesome\Model\Tests\Support\Model\{Address, Country, NestedPaths};

/**
 * Data provider for {@see \UIAwesome\Model\Tests\ModelNestedTest} test cases.
 */
final class ModelNestedProvider
{
    /**
     * @phpstan-return array<string, array{bool}>
     */
    public static function guardedModelPaths(): array
    {
        return [
            'absent nullable' => [true],
            'ambiguous present' => [false],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, class-string, string}>
     */
    public static function invalidModelPaths(): array
    {
        return [
            'deeper scalar suffix' => ['address.country.name.extra', Country::class, 'name.extra'],
            'empty middle segment' => ['address..city', Address::class, ''],
            'empty nested suffix' => ['required.city.', Address::class, 'city.'],
            'empty root' => ['.name', NestedPaths::class, ''],
            'empty scalar suffix' => ['name.', NestedPaths::class, 'name.'],
            'nested scalar suffix' => ['address.city.nonexistent', Address::class, 'city.nonexistent'],
            'scalar suffix' => ['name.nonexistent', NestedPaths::class, 'name.nonexistent'],
            'unknown nested leaf' => ['address.nonexistent', Address::class, 'nonexistent'],
            'unknown root' => ['missing.city', NestedPaths::class, 'missing'],
        ];
    }

    /**
     * @phpstan-return array<string, array{string, string}>
     */
    public static function nestedPropertyValues(): array
    {
        return [
            'profile address city' => ['profile.address.city', 'Voronezh'],
            'profile address country name' => ['profile.address.country.name', 'Russia'],
            'profile address street' => ['profile.address.street', 'ulitsa 9-ya Voronezhskaya'],
            'profile biography' => ['profile.bio', 'senior software engineer'],
            'user name' => ['name', 'valery'],
        ];
    }

    /**
     * @phpstan-return array<string, array{mixed}>
     */
    public static function nonModelBranchValues(): array
    {
        return [
            'array' => [[]],
            'bool' => [false],
            'int' => [0],
            'null' => [null],
            'object' => [new stdClass()],
            'string' => ['scalar'],
        ];
    }

    /**
     * @phpstan-return array<string, array{list<string>}>
     */
    public static function nullableModelTypes(): array
    {
        return [
            'null first' => [['null', Address::class]],
            'null last' => [[Address::class, 'null']],
        ];
    }

    /**
     * @phpstan-return array<string, array{string}>
     */
    public static function presentModelPaths(): array
    {
        return [
            'nullable deeper path' => ['address.country.name'],
            'nullable leaf' => ['address.city'],
            'required deeper path' => ['required.country.name'],
            'required leaf' => ['required.city'],
        ];
    }
}
