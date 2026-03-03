# ChangeLog

## 0.2.0 Under development

- Enh #2: Modernized the package API and documentation, including consistency improvements, structural cleanup, and migration guidance in `UPGRADE.md` (@terabytesoftw)
- Enh #3: Unified `load()` with `setProperties()` to apply consistent snake_case to camelCase mapping, reduced timestamp initialization overhead during bulk loads, and updated related tests (@terabytesoftw)
- Enh #4: Added explicit readonly write protection in `TypeCollector`, replacing fatal reassignment failures with `InvalidArgumentException`, and expanded test coverage for readonly initialization and reassignment paths (@terabytesoftw)
- Enh #5: Extended automatic type casting to support `DateTime` and `DateTimeImmutable` from string input, added explicit invalid-date errors (including overflow-normalized dates), and expanded coverage for date casting paths (@terabytesoftw)
- Enh #6: Added `#[MapFrom('key')]` for explicit payload-key mapping in `setProperties()` and `load()`, including support for non-snake_case keys and validation for duplicate mappings (@terabytesoftw)
- Enh #7: Added `#[Trim]` to normalize string input before assignment across `load()`, `setProperties()`, and `setPropertyValue()`, including nested-property coverage and mutation-tested edge cases (@terabytesoftw)
- Enh #8: Added `#[Cast]` with built-in `array` casting and pluggable custom caster classes via `CastValueInterface`, with full validation, error reporting, and mutation-tested coverage (@terabytesoftw)
- Bug #9: Fixed general model stability and consistency issues across core mapping behavior, internal documentation, and test coverage updates (@terabytesoftw)
- Bug #10: Fixed attribute test-suite organization by moving and isolating core attribute coverage into dedicated test classes for clearer maintenance (@terabytesoftw)
- Enh #11: Added `#[NoSnakeCase]` to preserve selected property names during `toArray(snakeCase: true)` serialization while keeping default conversion for other keys (@terabytesoftw)
- Enh #12: Added `#[DefaultValue]` to apply runtime defaults for `null` and empty-string inputs before custom casting and native type conversion (@terabytesoftw)
- Bug #13: Simplified `TypeCollector` internals by streamlining `DoNotCollect` detection, normalizing static-property checks, reducing dead fallback logic in nested-property split handling, and using first-class callable trimming while preserving behavior (@terabytesoftw)
- Bug #14: Simplified `AbstractModel::load()` by replacing a boolean `match` expression with an equivalent ternary assignment for clearer payload scope resolution while preserving behavior (@terabytesoftw)
- Bug #15: Consolidated `TypeCollector` metadata collection into a single reflection pass, removing repeated collector loops while preserving map-key validation and attribute-driven behavior (@terabytesoftw)
- Bug #16: Refactored `snakeCaseToCamelCase` method for improved readability and performance by consolidating logic into a single return statement (@terabytesoftw)
- Bug #17: Added dedicated regression coverage to ensure `TypeCollector::toArray()` preserves custom `getPropertyValue()` override behavior (@terabytesoftw)
- Enh #18: Simplified the model property API across public contracts, implementation, tests, and docs (@terabytesoftw)
- Bug #19: Better naming `AbstractModel` to `BaseModel` class and update related classes, tests, and documentation for clarity (@terabytesoftw)

## 0.1.0 March 18, 2024

- Initial release (@terabytesoftw)
