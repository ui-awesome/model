# Upgrade Guide

## 0.2.0

### PHP requirements

- The minimum PHP version is now `^8.3` (previously `^8.1`).

### Renamed and removed API

`AbstractModel` and most public methods were renamed for a shorter, consistent API. Update every call site and `use`
statement.

| Before (`0.1.0`)                | After (`0.2.0`)             |
| ------------------------------- | --------------------------- |
| `UIAwesome\Model\AbstractModel` | `UIAwesome\Model\BaseModel` |
| `addProperty()`                 | `add()`                     |
| `getProperties()`               | `getNames()`                |
| `getPropertiesTypes()`          | `getTypes()`                |
| `getPropertyValue()`            | `getValue()`                |
| `hasProperty()`                 | `has()`                     |
| `isPropertyType()`              | `isType()`                  |
| `setPropertiesValues()`         | `setValues()`               |
| `setPropertyValue()`            | `setValue()`                |

- The same method renames apply when using `TypeCollector` directly; its former `getProperties()` method is now
  `getTypes()`.
- `toArray()` named argument `exceptPropierties` was corrected to `exceptProperties`.
- `setValues()` renamed the former `setPropertiesValues()` second argument from `exceptPropierties` to `except`.
- `ModelInterface` now uses the renamed methods and declares
  `toArray(bool $snakeCase = false, array $exceptProperties = []): array`; direct implementers must update every
  signature.

### Behavioral changes

- `getTypes()` now includes `null` for nullable properties (for example `int|null` returns `['int', 'null']`).
- Nullable unions with one non-null type are now cast to that type; unions with multiple non-null types remain
  uncast.
- Property exclusions in `setValues()` are matched by camelCase names (for example `publicEmailPersonal`, not
  `public_email_personal`).
- `load()` normalizes snake_case payload keys to camelCase, so both `public_email_personal` and `publicEmailPersonal`
  bind to the same property, and it now supports generators and other `Traversable` payloads.
- Timestamp properties are initialized after a successful assignment when their value is `null` or `0`; `getValue()`
  no longer mutates timestamps while reading.
- Reassigning an already initialized `readonly` property now throws `InvalidArgumentException` instead of surfacing a
  PHP fatal error.
- String values assigned to `DateTime` or `DateTimeImmutable` properties are cast automatically; invalid strings,
  including overflow dates such as `2026-02-30`, now throw `InvalidArgumentException`.
- Non-numeric strings are no longer coerced to `0` for `int` and `float` properties, and incompatible values for
  typed properties now surface a native `TypeError`.
- Undefined-property and validation exception messages now use the public `Message` enum; update code that matches
  the exact `0.1.0` message text.
- `has()` now validates every segment of a nested path rather than reporting any dotted name as present.
- Registered dynamic values are kept in model collector storage instead of being created as PHP dynamic properties.
- `toArray(snakeCase: true)` no longer prefixes PascalCase property names with `_`, and properties marked with
  `#[NoSnakeCase]` preserve their declared key.

### New property metadata

- Use `#[MapFrom('external-key')]` to map explicit payload keys. Duplicate or empty keys are rejected.
- Use `#[Trim]` to trim string input before defaults and casts.
- Use `#[DefaultValue($value)]` to replace `null` or empty-string input before casting.
- Use `#[Cast('array')]` or a `CastValueInterface` implementation for custom conversion.
- Use `#[NoSnakeCase]` to preserve a property key during snake_case serialization.

`#[DoNotCollect]` and `#[Timestamp]` remain available; static and excluded properties do not contribute metadata.

### Package requirements

- `php-forge/helper ^0.3` is now a runtime dependency for reflection metadata.
- `ext-mbstring` is no longer required by the package.
- The package license changed from MIT to BSD-3-Clause.
