<?php

declare(strict_types=1);

namespace UIAwesome\Model;

/**
 * Interface implemented by classes supporting model data binding.
 */
interface ModelInterface
{
    /**
     * Adds a new property to the model.
     *
     * @param string $property The property name.
     * @param array|string $type The property type. This can be a string or an array of strings representing the
     * property type. If an array is given, the property is considered to be multi-valued and the property value should
     * be an array of values of the given types.
     *
     * @phpstan-param list<string>|string $type
     */
    public function addProperty(string $property, string|array $type): void;

    /**
     * Returns the raw data for the model.
     *
     * @return array The raw data for the model.
     *
     * @phpstan-return mixed[]
     */
    public function getData(): array;

    /**
     * Returns the model name that this model class should use.
     *
     * The model name is mainly used by {@see AbstractModel} to decide how to name the input fields for the properties
     * in a model.
     *
     * If the model name is "A" and an property name is "b", then the corresponding input name would be "A[b]".
     * If the model name is an empty string, then the input name would be "b".
     *
     * The purpose of the above naming schema is that for forms which contain multiple different models, the properties
     * of each model are grouped in sub-arrays of the POST-data, and it's easier to differentiate between them.
     *
     * By default, this method returns the model class name (without the namespace part) as the model name.
     * You may override it when the model is used in different forms.
     *
     * @return string The model name class, without a namespace part or empty string when class is anonymous.
     *
     * {@see load()}
     */
    public function getModelName(): string;

    /**
     * @return array The list of properties names.
     *
     * @phpstan-return mixed[]
     */
    public function getProperties(): array;

    /**
     * Returns the list of property types indexed by property names.
     *
     * @return array The list of property types indexed by property names.
     *
     * @phpstan-return mixed[]
     */
    public function getPropertyTypes(): array;

    /**
     * Returns the value (raw data) for the specified property.
     *
     * @param string $property The property name.
     *
     * @return mixed The value (raw data) for the specified property.
     */
    public function getPropertyValue(string $property): mixed;

    /**
     * Checks if the model has the specified property.
     *
     * @param string $property The property name.
     *
     * @return bool `true` if the model has the specified property, `false` otherwise.
     */
    public function hasProperty(string $property): bool;

    /**
     * Whether the model has no loaded raw data.
     */
    public function isEmpty(): bool;

    /**
     * Checks if the property is of the specified type.
     *
     * @param string $property The property name.
     * @param string $type The type name.
     *
     * @return bool `true` if the property is of the specified type, `false` otherwise.
     */
    public function isPropertyType(string $property, string $type): bool;

    /**
     * Populates the model with input data.
     *
     * The `load()` method gets the model name from the {@see getModelName()} method (which you may override), unless
     * the `$modelName` parameter is given.
     * If the model name is an empty string, `load()` populates the model with the whole `$data` array instead of
     * `$data['ModelName']`.
     *
     * @param iterable $data The data array to load, typically server request properties.
     * @param string|null $modelName The scope from which to get data.
     *
     * @return bool `true` if the model is successfully populated with some data, `false` otherwise.
     *
     * @phpstan-param mixed[] $data
     */
    public function load(iterable $data, string|null $modelName = null): bool;

    /**
     * Sets values for multiple properties.
     *
     * @param array $data The key-value pairs to set for the properties.
     * @param array $exceptProperties The properties to exclude from the setting using camelCase names.
     * If not empty, only the properties in this array will be set.
     * If empty, all properties will be set.
     *
     * @phpstan-param mixed[] $data
     * @phpstan-param mixed[] $exceptProperties
     */
    public function setProperties(array $data, array $exceptProperties = []): void;

    /**
     * Sets the value for the specified property.
     *
     * @param string $property The property name.
     * @param mixed $value The value to set.
     */
    public function setPropertyValue(string $property, mixed $value): void;

    /**
     * Returns model properties as an array.
     *
     * @param bool $snakeCase Whether keys should be converted to snake_case.
     * @param array $exceptProperties List of properties to exclude.
     *
     * @phpstan-param mixed[] $exceptProperties
     *
     * @return array<string, mixed>
     */
    public function toArray(bool $snakeCase = false, array $exceptProperties = []): array;
}
