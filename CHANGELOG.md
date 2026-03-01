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

## 0.1.0 March 18, 2024

- Initial release (@terabytesoftw)
