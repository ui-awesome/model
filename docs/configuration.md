# Configuration reference

## Overview

This guide describes model design conventions and behavior that affect runtime consistency.

## Model declaration

Create models by extending `AbstractModel` and declaring typed properties.

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\{DoNotCollect, MapFrom, Timestamp};

final class User extends AbstractModel
{
    public string $name = '';
    #[MapFrom('user-email-address')]
    public string $email = '';
    #[DoNotCollect]
    private string $internalToken = '';
    #[Timestamp]
    private int $updatedAt = 0;
}
```

## Load behavior

- `load(iterable $data, ?string $modelName = null)` accepts scoped and unscoped payloads.
- Input keys must match existing properties; undefined keys throw `InvalidArgumentException`.
- `load()` and `setProperties()` both support explicit input mapping via `#[MapFrom('external-key')]`.
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

- Use `getPropertyTypes()` to retrieve collected property types.
- Nullable properties include `null` in the collected metadata.

```php
$types = $model->getPropertyTypes();
// Example: ['age' => ['int', 'null']]
```

## Property assignment rules

- `setPropertyValue()` assigns a single property and supports nested paths (`profile.address.city`).
- `setProperties()` assigns multiple values and converts snake_case input keys to camelCase.
- `#[MapFrom('key')]` has priority over snake_case conversion for matching input payload keys.
- `setProperties($data, $exceptProperties)` exclusions are evaluated in camelCase.

```php
$model->setProperties(['public_email_personal' => 'dev@example.com'], ['publicEmailPersonal']);
$model->setProperties(['user-email-address' => 'dev@example.com']);
```

## Date object casting

- `DateTime` and `DateTimeImmutable` typed properties are cast automatically from string values.
- Invalid date strings and overflow-normalized dates (for example `2026-02-30`) throw `InvalidArgumentException`.

```php
$model->setPropertyValue('publishedAt', '2026-03-01T10:00:00+00:00');
```

## Array serialization

- `toArray(bool $snakeCase = false, array $exceptProperties = [])` exports model data.
- Use `snakeCase: true` to transform output keys.

```php
$payload = $model->toArray(snakeCase: true, exceptProperties: ['updatedAt']);
```

## Next steps

- 💡 [Usage examples](examples.md)
- 🧪 [Testing guide](testing.md)
