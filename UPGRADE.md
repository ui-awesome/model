# Upgrade Guide

## 0.2.0

### PHP requirements

- The minimum PHP version is now `^8.3` (previously `^8.1`).

### Renamed and removed API

`AbstractModel` and most public methods were renamed for a shorter, consistent API. Update every call site and `use`
statement.

| Before (`0.1.x`) | After (`0.2.0`) |
| --- | --- |
| `UIAwesome\Model\AbstractModel` | `UIAwesome\Model\BaseModel` |
| `addProperty()` | `add()` |
| `getProperties()` | `getNames()` |
| `getPropertiesTypes()` | `getTypes()` |
| `getPropertyValue()` | `getValue()` |
| `hasProperty()` | `has()` |
| `isPropertyType()` | `isType()` |
| `setProperties()` | `setValues()` |
| `setPropertyValue()` | `setValue()` |

- `setPropertiesValues()` was removed; use `setValues()`.
- `toArray()` named argument `exceptPropierties` was corrected to `exceptProperties`.
- `setValues()` (formerly `setProperties()`) renamed its second argument `exceptProperties` to `except`.
- `ModelInterface` now declares `toArray(bool $snakeCase = false, array $exceptProperties = []): array`; direct
  implementers must match this signature.

### Behavioral changes

- `getTypes()` now includes `null` for nullable properties (for example `int|null` returns `['int', 'null']`).
- Property exclusions in `setValues()` are matched by camelCase names (for example `publicEmailPersonal`, not
  `public_email_personal`).
- `load()` normalizes snake_case payload keys to camelCase, so both `public_email_personal` and `publicEmailPersonal`
  bind to the same property.
- `getValue()` no longer mutates timestamp properties while reading.
- Reassigning an already initialized `readonly` property now throws `InvalidArgumentException` instead of surfacing a
  PHP fatal error.
- String values assigned to `DateTime` or `DateTimeImmutable` properties are cast automatically; invalid strings,
  including overflow dates such as `2026-02-30`, now throw `InvalidArgumentException`.
