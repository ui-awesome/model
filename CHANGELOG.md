# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 0.2.0 Under development

- feat(model): modernize the package API and documentation, including consistency improvements, structural cleanup, and migration guidance in `UPGRADE.md`.
- feat(model): unify `load()` with `setProperties()` to apply consistent snake_case to camelCase mapping, reduce timestamp initialization overhead during bulk loads, and update related tests.
- feat(model): add explicit readonly write protection in `TypeCollector`, replacing fatal reassignment failures with `InvalidArgumentException`, and expand test coverage for readonly initialization and reassignment paths.
- feat(model): extend automatic type casting to support `DateTime` and `DateTimeImmutable` from string input, add explicit invalid-date errors (including overflow-normalized dates), and expand coverage for date casting paths.
- feat(attribute): add `#[MapFrom('key')]` for explicit payload-key mapping in `setProperties()` and `load()`, including support for non-snake_case keys and validation for duplicate mappings.
- feat(attribute): add `#[Trim]` to normalize string input before assignment across `load()`, `setProperties()`, and `setPropertyValue()`, including nested-property coverage and mutation-tested edge cases.
- feat(attribute): add `#[Cast]` with built-in `array` casting and pluggable custom caster classes via `CastValueInterface`, with full validation, error reporting, and mutation-tested coverage.
- fix(model): fix general model stability and consistency issues across core mapping behavior, internal documentation, and test coverage updates.
- fix(tests): fix attribute test-suite organization by moving and isolating core attribute coverage into dedicated test classes for clearer maintenance.
- feat(model): add `#[NoSnakeCase]` to preserve selected property names during `toArray(snakeCase: true)` serialization while keeping default conversion for other keys.
- feat(attribute): add `#[DefaultValue]` to apply runtime defaults for `null` and empty-string inputs before custom casting and native type conversion.
- fix(model): simplify `TypeCollector` internals by streamlining `DoNotCollect` detection, normalizing static-property checks, reducing dead fallback logic in nested-property split handling, and using first-class callable trimming while preserving behavior.
- fix(model): simplify `AbstractModel::load()` by replacing a boolean `match` expression with an equivalent ternary assignment for clearer payload scope resolution while preserving behavior.
- fix(model): consolidate `TypeCollector` metadata collection into a single reflection pass, removing repeated collector loops while preserving map-key validation and attribute-driven behavior.
- fix(model): refactor `snakeCaseToCamelCase` method for improved readability and performance by consolidating logic into a single return statement.
- fix(tests): add dedicated regression coverage to ensure `TypeCollector::toArray()` preserves custom `getPropertyValue()` override behavior.
- feat(model): simplify the model property API across public contracts, implementation, tests, and docs.
- fix(model): rename `AbstractModel` to `BaseModel` across source, tests, and documentation; see `UPGRADE.md` for migration steps.
- fix(model): refactor model metadata internals to use `PHPForge\Helper\Reflector` for reflection, property attributes, and model short-name resolution while preserving public behavior.
- chore: update dependencies and configuration files.
- chore: update configuration files and dependencies, improve linter settings.

## 0.1.0 March 18, 2024

- chore: initial release.
