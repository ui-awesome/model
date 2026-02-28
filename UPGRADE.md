# Upgrade Guide

## 0.2.0

### Breaking changes

- `setPropertiesValues()` was removed.
- `toArray()` named argument `exceptPropierties` was renamed to `exceptProperties`.
- `ModelInterface` now requires implementing `toArray(bool $snakeCase = false, array $exceptProperties = []): array`.
- `getPropertiesTypes()` was renamed to `getPropertyTypes()`.
- Nullable declared properties now expose type metadata including `null` (for example `int|null` is now `['int', 'null']`).
- `setProperties($data, $exceptProperties)` now matches exclusions using camelCase property names.
- `getPropertyValue()` no longer mutates timestamp properties while reading.
- `load()` now uses the same key normalization as `setProperties()`, mapping snake_case payload keys to camelCase model properties.

### Migration steps

- Replace all calls to `setPropertiesValues($data, $exceptProperties)` with `setProperties($data, $exceptProperties)`.
- Replace all named-argument calls `toArray(exceptPropierties: [...])` with `toArray(exceptProperties: [...])`.
- If you implement `ModelInterface` directly, add the new `toArray()` method with the exact signature.
- Replace all calls to `getPropertiesTypes()` with `getPropertyTypes()`.
- If your code asserts exact output from `getPropertyTypes()`, update expectations for nullable properties to include `'null'`.
- Update `setProperties()` exclusions to camelCase names, e.g. `publicEmailPersonal` instead of `public_email_personal`.
- Review `load()` payload keys if your models intentionally use underscored property names, because snake_case keys are now normalized to camelCase during assignment.

```php
<?php

declare(strict_types=1);

// Before
$model->setPropertiesValues($data, $exceptProperties);

$model->toArray(exceptPropierties: ['pathAvatar']);

$types = $model->getPropertiesTypes();

$model->setProperties($data, ['public_email_personal']);

// After
$model->setProperties($data, $exceptProperties);

$model->toArray(exceptProperties: ['pathAvatar']);

$types = $model->getPropertyTypes();

$model->setProperties($data, ['publicEmailPersonal']);

$model->load(['Profile' => ['publicEmailPersonal' => 'admin@example.com']]);

// snake_case now also works in load()
$model->load(['Profile' => ['public_email_personal' => 'admin@example.com']]);
```
