# ChangeLog

## 0.2.0 Under development

- Enh #2: Modernized the package API and documentation, including consistency improvements, structural cleanup, and migration guidance in `UPGRADE.md` (@terabytesoftw)
- Enh #3: Unified `load()` with `setProperties()` to apply consistent snake_case to camelCase mapping, reduced timestamp initialization overhead during bulk loads, and updated related tests (@terabytesoftw)
- Enh #4: Added explicit readonly write protection in `TypeCollector`, replacing fatal reassignment failures with `InvalidArgumentException`, and expanded test coverage for readonly initialization and reassignment paths (@terabytesoftw)

## 0.1.0 March 18, 2024

- Initial release (@terabytesoftw)
