# Upgrade Guide

## 0.2.0

### Renamed API

| Before                  | After         |
| ----------------------- | ------------- |
| `AbstractModel`         | `BaseModel`   |
| `addProperty()`         | `add()`       |
| `getProperties()`       | `getNames()`  |
| `getPropertiesTypes()`  | `getTypes()`  |
| `getPropertyValue()`    | `getValue()`  |
| `hasProperty()`         | `has()`       |
| `isPropertyType()`      | `isType()`    |
| `setPropertiesValues()` | `setValues()` |
| `setPropertyValue()`    | `setValue()`  |

`TypeCollector::getProperties()` was renamed to `getTypes()`.

The `exceptPropierties` named argument was corrected:

- `toArray()` now uses `exceptProperties`.
- `setValues()` now uses `except`.

Direct `ModelInterface` implementations must update their method names and signatures.

### Behavioral changes

- `getTypes()` includes `null` for nullable properties.
- Nullable unions with one non-null type are cast to that type.
- `load()` and `setValues()` normalize snake_case keys to camelCase.
- `setValues()` exclusions use camelCase property names.
- `load()` accepts `Traversable` payloads.
- Timestamp initialization occurs after assignment; `getValue()` no longer mutates timestamps.
- Reassigning an initialized `readonly` property throws `InvalidArgumentException`.
- Strings assigned to `DateTime` and `DateTimeImmutable` are validated and cast.
- Invalid typed values are no longer coerced silently.
- `has()` validates every segment of nested paths.
- Registered dynamic values remain in collector storage instead of becoming dynamic PHP properties.
- `toArray(snakeCase: true)` no longer prefixes PascalCase names with `_`.
- Exception messages now come from the public `Message` enum.

### Property metadata

The following attributes are available:

- `#[MapFrom('external-key')]` for explicit input keys.
- `#[Trim]` for string normalization.
- `#[DefaultValue($value)]` for null or empty-string fallbacks.
- `#[Cast('array')]` or `CastValueInterface` for custom conversion.
- `#[NoSnakeCase]` to preserve serialized keys.

Static and `#[DoNotCollect]` properties are skipped during metadata collection.
