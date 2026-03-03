# Configuration reference

## Overview

This guide describes model design conventions and behavior that affect runtime consistency.

## Model declaration

Create models by extending `BaseModel` and declaring typed properties.

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\Attribute\{Cast, DefaultValue, DoNotCollect, MapFrom, NoSnakeCase, Timestamp, Trim};
use UIAwesome\Model\BaseModel;

final class User extends BaseModel
{
    #[NoSnakeCase]
    public string $apiVersion = 'v1';

    #[DefaultValue('Guest')]
    public string $displayName = '';

    #[MapFrom('user-email-address')]
    public string $email = '';

    #[DoNotCollect]
    private string $internalToken = '';

    #[Trim]
    public string $name = '';

    #[Trim]
    public string $publicEmailPersonal = '';

    #[Cast('array')]
    public array $tags = [];

    #[Timestamp]
    private int $updatedAt = 0;
}
```

## Load behavior

- `load(iterable $data, ?string $modelName = null)` accepts scoped and unscoped payloads.
- Input keys must match existing properties; undefined keys throw `InvalidArgumentException`.
- `load()` and `setValues()` both support explicit input mapping via `#[MapFrom('external-key')]`.
- `isEmpty()` indicates whether loaded raw data is empty.

```php
$model->load(
    [
        'User' => [
            'name' => 'Ada Lovelace',
            'user-email-address' => 'ada@example.com',
        ],
    ],
);
```

## Type metadata

- Use `getTypes()` to retrieve collected property types.
- Nullable properties include `null` in the collected metadata.

```php
$types = $model->getTypes();
/*
[
    'apiVersion' => 'string',
    'displayName' => 'string',
    'email' => 'string',
    'name' => 'string',
    'publicEmailPersonal' => 'string',
    'tags' => 'array',
    'updatedAt' => 'timestamp',
]
*/
```

## Property assignment rules

- `setValue()` assigns a single property and supports nested paths (`profile.address.city`).
- `setValues()` assigns multiple values and converts snake_case input keys to camelCase.
- `#[MapFrom('key')]` has priority over snake_case conversion for matching input payload keys.
- `#[Trim]` trims string values before type casting and assignment.
- `#[DefaultValue(...)]` applies runtime defaults when assigned values are `null` or `''`.
- `#[Cast('array')]` converts comma-separated strings into arrays before native property casting.
- `#[Cast(YourCaster::class)]` supports custom casting classes implementing `CastValueInterface`.
- `setValues($data, $except)` exclusions are evaluated in camelCase.

```php
$model->setValues(
    ['public_email_personal' => 'dev@example.com'],
    ['publicEmailPersonal'],
);
$model->setValues(
    ['user-email-address' => 'dev@example.com'],
);
$model->setValue('displayName', '');
// displayName => 'Guest'
```

## Date object casting

- `DateTime` and `DateTimeImmutable` typed properties are cast automatically from string values.
- Invalid date strings and overflow-normalized dates (for example `2026-02-30`) throw `InvalidArgumentException`.

```php
$model->setValue('publishedAt', '2026-03-01T10:00:00+00:00');
```

## Array serialization

- `toArray(bool $snakeCase = false, array $exceptProperties = [])` exports model data.
- Use `snakeCase: true` to transform output keys.
- `#[NoSnakeCase]` preserves marked property names when `snakeCase: true` is enabled.

```php
$payload = $model->toArray(snakeCase: true, exceptProperties: ['updatedAt']);
/*
[
    'apiVersion' => 'v1',
    'display_name' => 'Guest',
    'email' => 'dev@example.com',
    'name' => 'Ada Lovelace',
    'public_email_personal' => 'dev@example.com',
    'tags' => [],
]
*/
```

## Next steps

- 💡 [Usage examples](examples.md)
- 🧪 [Testing guide](testing.md)
