# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 0.2.1 Under development

## 0.2.0 July 20, 2026

- feat(model)!: rename `AbstractModel` to `BaseModel` and simplify the public model and collector APIs; see `UPGRADE.md`.
- feat(model)!: add `toArray()` to `ModelInterface` and correct the `exceptPropierties` named argument.
- feat(model)!: normalize snake_case input in `load()` and `setValues()` and match exclusions by resolved camelCase names.
- feat(model): support scoped loading from generators and other `Traversable` payloads.
- feat(model)!: include `null` in nullable metadata and cast unions containing one non-null type to that type.
- feat(model): cast strings to `DateTime` and `DateTimeImmutable` with validation for invalid and overflow dates.
- feat(model)!: reject incompatible typed assignments instead of coercing arbitrary values.
- feat(model)!: initialize zero or null timestamps after assignments while keeping timestamp reads stable.
- feat(model): protect initialized `readonly` properties with descriptive `InvalidArgumentException` errors.
- feat(model)!: store registered dynamic values without creating deprecated PHP dynamic properties.
- feat(model)!: validate nested paths in `has()` and property assignment.
- feat(attribute): add `#[Cast]` and custom casters implementing `CastValueInterface`.
- feat(attribute): add `#[DefaultValue]`, `#[MapFrom]`, `#[NoSnakeCase]`, and `#[Trim]`.
- feat(exception)!: add the `Message` enum and standardize exception messages.
- fix(model): preserve custom `getValue()` overrides during serialization and avoid leading underscores for PascalCase keys.
- fix(model): skip metadata from static and `#[DoNotCollect]` properties without stopping collection for later properties.
- chore!: require PHP `^8.3` and `php-forge/helper ^0.3`, and remove unused `ext-mbstring` and `php-forge/support` requirements.
- chore!: change the package license from MIT to BSD-3-Clause.
- fix(model): simplify nested type, date validation, and attribute metadata checks with regression coverage for 100% mutation testing.

## 0.1.0 March 18, 2024

- chore: initial release.
