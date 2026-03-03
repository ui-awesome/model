<?php

declare(strict_types=1);

namespace UIAwesome\Model;

/**
 * Interface implemented by classes supporting model data binding.
 *
 * Usage example:
 * ```php
 * final class UserForm extends BaseModel
 * {
 *     public string $name = '';
 * }
 *
 * $model = new UserForm();
 * $model->load(['UserForm' => ['name' => 'Ada']]);
 * ```
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
interface ModelInterface
{
    /**
     * Adds a new property to the model.
     *
     * Usage example:
     * ```php
     * $model->add('name', 'string');
     * ```
     *
     * @param string $property Property name.
     * @param array|string $type Property type. This can be a string or an array of strings representing the property
     * type. If an array is given, the property is considered to be multi-valued and the property value should be an
     * array of values of the given types.
     *
     * @phpstan-param list<string>|string $type
     */
    public function add(string $property, string|array $type): void;

    /**
     * Returns the raw data for the model.
     *
     * Usage example:
     * ```php
     * $data = $model->getData();
     * ```
     *
     * @return array Raw data for the model.
     *
     * @phpstan-return mixed[]
     */
    public function getData(): array;

    /**
     * Returns the model name that this model class should use.
     *
     * The model name is mainly used by {@see BaseModel} to decide how to name the input fields for the properties
     * in a model.
     * - If the model name is "A" and an property name is "b", then the corresponding input name would be "A[b]".
     * - If the model name is an empty string, then the input name would be "b".
     *
     * The purpose of the above naming schema is that for forms which contain multiple different models, the properties
     * of each model are grouped in sub-arrays of the POST-data, and it's easier to differentiate between them.
     * - By default, this method returns the model class name (without the namespace part) as the model name.
     * - You may override it when the model is used in different forms.
     *
     * Usage example:
     * ```php
     * public function getModelName(): string
     * {
     *     return 'UserForm';
     * }
     * ```
     *
     * @return string Model name class, without a namespace part or empty string when class is anonymous.
     *
     * {@see load()}
     */
    public function getModelName(): string;

    /**
     * Returns the list of property names.
     *
     * Usage example:
     * ```php
     * $properties = $model->getNames();
     * ```
     *
     * @return list<string> List of property names.
     *
     * @phpstan-return list<string>
     */
    public function getNames(): array;

    /**
     * Returns the list of property types indexed by property names.
     *
     * Usage example:
     * ```php
     * $propertyTypes = $model->getTypes();
     * ```
     *
     * @return array List of property types indexed by property names.
     *
     * @phpstan-return mixed[]
     */
    public function getTypes(): array;

    /**
     * Returns the value (raw data) for the specified property.
     *
     * Usage example:
     * ```php
     * $name = $model->getValue('name');
     * ```
     *
     * @param string $property Property name.
     *
     * @return mixed Value (raw data) for the specified property.
     */
    public function getValue(string $property): mixed;

    /**
     * Checks if the model has the specified property.
     *
     * Usage example:
     * ```php
     * if ($model->has('name')) {
     *    // ...
     * }
     * ```
     *
     * @param string $property Property name.
     *
     * @return bool `true` if the model has the specified property, `false` otherwise.
     */
    public function has(string $property): bool;

    /**
     * Whether the model has no loaded raw data.
     *
     * Usage example:
     * ```php
     * if ($model->isEmpty()) {
     *    // ...
     * }
     * ```
     * @return bool `true` if the model has no loaded raw data, `false` otherwise.
     */
    public function isEmpty(): bool;

    /**
     * Checks if the property is of the specified type.
     *
     * Usage example:
     * ```php
     * if ($model->isType('name', 'string')) {
     *   // ...
     * }
     * ```
     *
     * @param string $property Property name.
     * @param string $type Type name.
     *
     * @return bool `true` if the property is of the specified type, `false` otherwise.
     */
    public function isType(string $property, string $type): bool;

    /**
     * Populates the model with input data.
     *
     * The `load()` method gets the model name from the {@see getModelName()} method (which you may override), unless
     * the `$modelName` parameter is given.
     * If the model name is an empty string, `load()` populates the model with the whole `$data` array instead of
     * `$data['ModelName']`.
     *
     * Usage example:
     * ```php
     * $model->load($_POST);
     * ```
     *
     * @param iterable $data Data array to load, typically server request properties.
     * @param string|null $modelName Scope from which to get data.
     *
     * @return bool `true` if the model is successfully populated with some data, `false` otherwise.
     *
     * @phpstan-param mixed[] $data
     */
    public function load(iterable $data, string|null $modelName = null): bool;

    /**
     * Sets the value for the specified property.
     *
     * Usage example:
     * ```php
     * $model->setValue('name', 'Ada');
     * ```
     *
     * @param string $property Property name.
     * @param mixed $value Value to set.
     */
    public function setValue(string $property, mixed $value): void;

    /**
     * Sets values for multiple properties.
     *
     * Usage example:
     * ```php
     * $model->setValues(['name' => 'Ada']);
     * ```
     *
     * @param array $data Key value pairs to set for the properties.
     * @param array $except Properties to exclude from the setting using camelCase names. If not empty, the listed
     * properties are skipped. If empty, all properties from `$data` are applied.
     *
     * @phpstan-param array<array-key, mixed> $data
     * @phpstan-param list<string> $except
     */
    public function setValues(array $data, array $except = []): void;

    /**
     * Returns model properties as an array.
     *
     * Usage example:
     * ```php
     * $array = $model->toArray();
     * ```
     *
     * @param bool $snakeCase Whether keys should be converted to snake_case.
     * @param array $exceptProperties List of properties to exclude.
     *
     * @phpstan-param list<string> $exceptProperties
     *
     * @return array<string, mixed>
     */
    public function toArray(bool $snakeCase = false, array $exceptProperties = []): array;
}
