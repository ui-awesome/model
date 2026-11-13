---
name: ui-awesome-model
description: Use when mapping input data to typed PHP models, reading or writing nested values with dot notation, or serializing models with ui-awesome/model. Not for database models, validation, form rendering or Yii ActiveRecord.
license: BSD-3-Clause
---

# Work with typed model mapping and nested values

## Resolve the actual package

Inspect the installed package path, reference and PHP constraint before choosing an API. This checkout currently requires
PHP ^8.3. Compare Composer installed metadata, the lockfile and actual source; modified/path repositories
may differ from a published revision. A skill installation does not install runtime dependencies.

Start with `src/BaseModel.php` and only the related source, tests and guide needed for the task. Guides may be excluded from
Composer archives: use the installed source or matching-revision repository documentation, not an assumed latest API.
Do not use this package as a substitute for unrelated Yii widgets, database models or frontend asset management.

## Package-specific workflow

- Use `BaseModel` and the actual `ModelInterface` for mapping data; this package is not a database model or validation framework.

- Inspect `load()` and its model-name scope before passing request data rather than assuming Yii semantics.

- Attributes such as `MapFrom`, `Trim`, `Cast`, `DefaultValue`, `Timestamp`, `NoSnakeCase` and `DoNotCollect` change collection/mapping behavior. Read `TypeCollector` for the selected attribute and property type.

- Test nested paths with present null values, absent properties and cloned models. Do not infer deep isolation from the PHP clone operation alone; verify mutation of the actual nested state.

## Preserve package contracts

These behaviors are intended or tested. Do not "fix" them while working on something else.

- `load()` uses `$data[$scope]` only when that key holds an array; otherwise it assigns the whole input, including the
  scope key itself. It returns `false` for empty scoped data. `getData()` returns the raw input keys before mapping, and
  `isEmpty()` reflects loaded raw data only, not values assigned with `setValue()`.
- Unknown string keys throw `InvalidArgumentException` instead of being ignored; integer keys are skipped.
- `setValues()` `$except` and `toArray()` `$exceptProperties` take camelCase property names, not input keys.
  Snake_case input keys and `MapFrom` keys both resolve to the property, and the property name stays accepted.
- A single-property assignment applies `Trim`, then `DefaultValue` (for `null` or `''`), then `Cast`, then the type cast
  for the declared type. `int` and `float` cast only numeric values, so a non-numeric string for an `int` property
  raises a `TypeError`. Union types with more than one non-null type are not cast.
- `bool` uses a PHP `(bool)` cast, so the string `'false'` becomes `true`. Tests assert only the resulting type; report
  this behavior instead of changing it.
- `Timestamp` properties receive `time()` when their value is `null` or `0` after `setValue()`, or after a
  `setValues()` call that assigns at least one property.
- `toArray()` returns nested models as objects, not arrays; `$snakeCase` converts only top-level keys.
- Reading, writing or checking a path through a nested model that is `null` throws, or returns `false` from `has()`.
  `getNames()` lists such a property as a leaf.
- A clone isolates its collector state, but nested model objects stay shared unless the model's `__clone()` copies
  them. Overwriting an initialized readonly property throws `InvalidArgumentException`.

## Verify the result

Execute focused examples against the inspected package and its actual dependencies. Assert normal, boundary and negative
cases with explicit failures; do not rely on disabled PHP assertions. Preserve supported PHP versions and report missing
runtime/tooling rather than changing constraints to manufacture a pass. Report what was executed separately from what was
only written. Do not install dependencies or modify unrelated files without an authorized task.
