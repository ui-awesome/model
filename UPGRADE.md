# Upgrade Guide

## 0.2.0

### Breaking changes

- `setPropertiesValues()` was removed.
- `toArray()` named argument `exceptPropierties` was renamed to `exceptProperties`.
- `ModelInterface` now requires implementing `toArray(bool $snakeCase = false, array $exceptProperties = []): array`.
- `getPropertiesTypes()` was renamed to `getPropertyTypes()`.
- `getPropertyValue(string $property): mixed` was renamed to `getValue(string $property): mixed`.
- `setPropertyValue(string $property, mixed $value): void` was renamed to `setValue(string $property, mixed $value): void`.
- `getProperties(): array` was renamed to `getNames(): array`.
- `hasProperty(string $property): bool` was renamed to `has(string $property): bool`.
- `addProperty(string $property, string|array $type): void` was renamed to `add(string $property, string|array $type): void`.
- `getPropertyTypes(): array` was renamed to `getTypes(): array`.
- `isPropertyType(string $property, string $type): bool` was renamed to `isType(string $property, string $type): bool`.
- `setProperties(array $data, array $exceptProperties = []): void` was renamed to `setValues(array $data, array $except = []): void`.
- Internal helper `getNestedProperties(ModelInterface $model, string $prefix): array` was renamed to `getNestedNames(ModelInterface $model, string $prefix): array`.
- Nullable declared properties now expose type metadata including `null` (for example `int|null` is now `['int', 'null']`).
- `setProperties($data, $exceptProperties)` now matches exclusions using camelCase property names.
- `getValue()` no longer mutates timestamp properties while reading.
- `load()` now uses the same key normalization as `setProperties()`, mapping snake_case payload keys to camelCase model properties.
- Assigning a value to an already initialized `readonly` property now throws `InvalidArgumentException` instead of surfacing a PHP fatal error.
- Automatic type casting now supports `DateTime` and `DateTimeImmutable` when string values are assigned to properties declared with those types.
- Invalid date/time strings now throw `InvalidArgumentException` with a clear casting error message.
- Overflow-normalized dates (for example `2026-02-30`) are now rejected as invalid instead of being silently normalized.

### Migration steps

- Replace all calls to `setPropertiesValues($data, $exceptProperties)` with `setProperties($data, $exceptProperties)`.
- Replace all named-argument calls `toArray(exceptPropierties: [...])` with `toArray(exceptProperties: [...])`.
- If you implement `ModelInterface` directly, add the new `toArray()` method with the exact signature.
- Replace all calls to `getPropertiesTypes()` with `getPropertyTypes()`.
- Replace all calls to `getPropertyValue($property)` with `getValue($property)`.
- Replace all calls to `setPropertyValue($property, $value)` with `setValue($property, $value)`.
- Replace all calls to `getProperties()` with `getNames()`.
- Replace all calls to `hasProperty($property)` with `has($property)`.
- Replace all calls to `addProperty($property, $type)` with `add($property, $type)`.
- Replace all calls to `getPropertyTypes()` with `getTypes()`.
- Replace all calls to `isPropertyType($property, $type)` with `isType($property, $type)`.
- Replace all calls to `setProperties($data, $exceptProperties)` with `setValues($data, $except)`.
- If you call internal/private methods via reflection, replace `getNestedProperties($model, $prefix)` with `getNestedNames($model, $prefix)`.
- If you call `setValues()` with named arguments, rename `exceptProperties:` to `except:`.
- If you override `load()`, keep it aligned with `setValues()` for bulk assignment behavior.
- If your code asserts exact output from `getPropertyTypes()`, update expectations for nullable properties to include `'null'`.
- Update `setProperties()` exclusions to camelCase names, e.g. `publicEmailPersonal` instead of `public_email_personal`.
- Review `load()` payload keys if your models intentionally use underscored property names, because snake_case keys are now normalized to camelCase during assignment.
- Handle `readonly` reassignment attempts as application-level exceptions when using `setValue()` or `setProperties()`.
- Validate incoming date strings before assignment if inputs are user-provided, because invalid values now fail fast with `InvalidArgumentException`.
- Update tests or input sanitization if your code previously relied on PHP date overflow normalization.

```php
<?php

declare(strict_types=1);

// Before
$model->setPropertiesValues($data, $exceptProperties);

$model->toArray(exceptPropierties: ['pathAvatar']);

$types = $model->getPropertiesTypes();

$value = $model->getPropertyValue('displayName');

$model->setPropertyValue('displayName', 'Ada');

$names = $model->getProperties();

$exists = $model->hasProperty('displayName');

$model->addProperty('dynamicFlag', 'bool');

$types = $model->getPropertyTypes();

$isString = $model->isPropertyType('displayName', 'string');

$model->setProperties($data, ['public_email_personal']);

// After
$model->setValues($data, $except);

$model->toArray(exceptProperties: ['pathAvatar']);

$types = $model->getTypes();

$value = $model->getValue('displayName');

$model->setValue('displayName', 'Ada');

$names = $model->getNames();

$exists = $model->has('displayName');

$model->add('dynamicFlag', 'bool');

$types = $model->getTypes();

$isString = $model->isType('displayName', 'string');

$model->setValues($data, ['publicEmailPersonal']);

$model->load(['Profile' => ['publicEmailPersonal' => 'admin@example.com']]);

// snake_case now also works in load()
$model->load(['Profile' => ['public_email_personal' => 'admin@example.com']]);

// readonly reassignment now throws InvalidArgumentException
$model->setValue('readonlyField', 'initial-value');
$model->setValue('readonlyField', 'new-value');

// DateTime and DateTimeImmutable now cast from strings
$model->setValue('createdAt', '2026-02-28 10:30:00');

// invalid date/time strings now throw InvalidArgumentException
$model->setValue('createdAt', 'not-a-date');
```
