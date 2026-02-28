<?php

declare(strict_types=1);

namespace UIAwesome\Model;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use UIAwesome\Model\Attribute\{DoNotCollect, Timestamp};
use UIAwesome\Model\Exception\Message;

use function array_filter;
use function array_key_exists;
use function array_key_last;
use function array_keys;
use function array_slice;
use function array_values;
use function count;
use function explode;
use function implode;
use function in_array;
use function is_a;
use function is_array;
use function is_object;
use function is_scalar;
use function method_exists;
use function str_contains;

/**
 * Collects and manages model property types and values.
 *
 * Usage example:
 * ```php
 * $collector = new TypeCollector($model);
 * $collector->setPropertyValue('name', 'Ada');
 * ```
 */
final class TypeCollector
{
    /**
     * @phpstan-var mixed[]
     */
    private array $dynamicValues = [];

    /**
     * @phpstan-var array<string, list<string>|string>
     */
    private array $properties = [];

    /**
     * @var ReflectionClass<ModelInterface>
     */
    private ReflectionClass $reflection;

    public function __construct(private readonly ModelInterface $model)
    {
        $this->reflection = new ReflectionClass($this->model);
        $this->properties = $this->collectProperties();
    }

    /**
     * Adds a new property to the list of properties types.
     *
     * Usage example:
     * ```php
     * $collector->addProperty('age', 'int');
     * $collector->addProperty('tags', ['string', 'null']);
     * ```
     *
     * @param string $property Name of the property.
     * @param array|string $type Type of the property.
     *
     * @phpstan-param list<string>|string $type
     */
    public function addProperty(string $property, string|array $type): void
    {
        $this->properties[$property] = $type;
    }

    /**
     * Returns the list of properties types indexed by property name.
     *
     * Usage example:
     * ```php
     * $propertyTypes = $collector->getPropertyTypes();
     * ```
     *
     * @return array List of properties types indexed by property name.
     *
     * @phpstan-return array<string, list<string>|string>
     */
    public function getPropertyTypes(): array
    {
        return $this->properties;
    }

    /**
     * Returns the value of a property.
     *
     * Dot notation is supported for nested models, for example `profile.email`.
     *
     * Usage example:
     * ```php
     * $email = $collector->getPropertyValue('profile.email');
     * ```
     *
     * @param string $property Property name.
     *
     * @return mixed Value of the property.
     */
    public function getPropertyValue(string $property): mixed
    {
        [$currentProperty, $nestedProperty] = $this->splitProperty($property);

        $currentPropertyValue = $this->readProperty($currentProperty);

        if ($nestedProperty !== null && $currentPropertyValue instanceof ModelInterface) {
            return $currentPropertyValue->getPropertyValue($nestedProperty);
        }

        return $currentPropertyValue;
    }

    /**
     * Checks whether a property exists.
     *
     * Dot notation is supported for nested models, for example `profile.email`.
     *
     * Usage example:
     * ```php
     * $hasEmail = $collector->hasProperty('profile.email');
     * ```
     *
     * @param string $property Property name.
     *
     * @return bool `true` if the property exists, `false` otherwise.
     */
    public function hasProperty(string $property): bool
    {
        [$property, $nested] = $this->splitProperty($property);

        $properties = $this->getPropertyTypes();

        if (!array_key_exists($property, $properties)) {
            return false;
        }

        if ($nested === null) {
            return true;
        }

        $propertyTypes = $properties[$property];

        if (!is_string($propertyTypes) || $propertyTypes === '' || !is_a($propertyTypes, ModelInterface::class, true)) {
            return false;
        }

        $propertyValue = $this->readProperty($property);

        if (!$propertyValue instanceof ModelInterface) {
            return false;
        }

        return $propertyValue->hasProperty($nested);
    }

    /**
     * Checks whether a property supports the specified type.
     *
     * Usage example:
     * ```php
     * $isString = $collector->isPropertyType('name', 'string');
     * ```
     *
     * @param string $property Property name.
     * @param string $type Type name.
     *
     * @return bool `true` if the property supports the specified type, `false` otherwise.
     */
    public function isPropertyType(string $property, string $type): bool
    {
        $propertyTypes = $this->properties[$property] ?? '';

        return is_string($propertyTypes) ? $propertyTypes === $type : in_array($type, $propertyTypes, true);
    }

    /**
     * Converts the value of an property to the type specified by type hinting in the PHPDoc.
     *
     * Usage example:
     * ```php
     * $age = $collector->phpTypeCast('age', '30'); // returns int(30) if the 'age' property is typed as int.
     * ```
     *
     * @param string $property Property name.
     * @param mixed $value Value to be converted.
     *
     * @return mixed Value of the property converted to the type specified by PHPDoc.
     */
    public function phpTypeCast(string $property, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $expectedTypes = $this->properties[$property] ?? null;

        if ($expectedTypes === null) {
            return null;
        }

        if (is_array($expectedTypes)) {
            $typeForCasting = $this->resolveTypeForCasting($expectedTypes);

            return $typeForCasting === null ? $value : $this->performTypeCasting($typeForCasting, $value);
        }

        return $this->performTypeCasting($expectedTypes, $value);
    }

    /**
     * Sets values for multiple properties at once.
     *
     * Usage example:
     * ```php
     * $collector->setProperties(
     *     [
     *         'name' => 'Ada',
     *         'age' => '30',
     *     ],
     * );
     * ```
     *
     * @phpstan-param array<array-key, mixed> $data
     * @phpstan-param list<string> $exceptProperties Camel-case property names to exclude.
     */
    public function setProperties(array $data, array $exceptProperties = []): void
    {
        $hasAssignments = false;

        foreach ($data as $property => $value) {
            if (is_string($property)) {
                $camelCaseName = $this->snakeCaseToCamelCase($property);

                if (in_array($camelCaseName, $exceptProperties, true)) {
                    continue;
                }

                $this->setPropertyValueInternal($camelCaseName, $value);

                $hasAssignments = true;
            }
        }

        if ($hasAssignments) {
            $this->initializeTimestampProperties();
        }
    }

    /**
     * Sets a value for one property.
     *
     * Dot notation is supported for nested models, for example `profile.email`.`
     *
     * Usage example:
     * ```php
     * $collector->setPropertyValue('profile.email', 'ada@example.com');
     * ```
     *
     * @param string $property The property name.
     * @param mixed $value The value to assign.
     */
    public function setPropertyValue(string $property, mixed $value): void
    {
        $this->setPropertyValueInternal($property, $value);
        $this->initializeTimestampProperties();
    }

    /**
     * Returns an array representation of the model properties and their values.
     *
     * Usage example:
     * ```php
     * $array = $collector->toArray(true, ['password']);
     * ```
     * @param bool $snakeCase Whether to convert property names to snake_case.
     * @param array $exceptProperties camelCase property names to exclude from the resulting array.
     *
     * @return array An array representation of the model properties and their values.
     *
     * @phpstan-param list<string> $exceptProperties
     * @phpstan-return array<string, mixed>
     */
    public function toArray(bool $snakeCase, array $exceptProperties = []): array
    {
        /** @var list<string> $properties */
        $properties = array_keys($this->getPropertyTypes());
        $result = [];

        foreach ($properties as $property) {
            if (!in_array($property, $exceptProperties, true)) {
                $value = $this->model->getPropertyValue($property);

                if ($snakeCase) {
                    $property = $this->camelCaseToSnakeCase($property);
                }

                $result[$property] = $value;
            }
        }

        return $result;
    }

    /**
     * Converts a camelCase formatted string to snake_case.
     *
     * @param string $value camelCase formatted string to convert.
     *
     * @return string Converted snake_case string.
     */
    private function camelCaseToSnakeCase(string $value): string
    {
        $snakeCase = preg_replace('/(?<!^)[A-Z]/', '_$0', $value);

        return strtolower((string) $snakeCase);
    }

    /**
     * Returns the list of property types indexed by property names.
     *
     * By default, this method returns all non-static properties of the class.
     *
     * @return array List of property types indexed by property names.
     *
     * @phpstan-return array<string, list<string>|string>
     */
    private function collectProperties(): array
    {
        $properties = [];

        foreach ($this->reflection->getProperties() as $property) {
            if ($property->isStatic() === false && !$this->hasDoNotCollectAttribute($property)) {
                /** @var ReflectionNamedType|ReflectionUnionType|null $type */
                $type = $property->getType();

                if ($type !== null) {
                    if ($type instanceof ReflectionUnionType) {
                        $typeNames = [];

                        foreach ($type->getTypes() as $unionType) {
                            if ($unionType instanceof ReflectionNamedType) {
                                $typeNames[] = $unionType->getName();
                            }
                        }

                        $properties[$property->getName()] = $typeNames;
                    } else {
                        $typeName = $type->getName();

                        if ($type->allowsNull() && $typeName !== 'null') {
                            $properties[$property->getName()] = [$typeName, 'null'];
                        } else {
                            $properties[$property->getName()] = $typeName;
                        }
                    }
                } else {
                    $properties[$property->getName()] = '';
                }

                if ($property->getAttributes(Timestamp::class) !== []) {
                    $properties[$property->getName()] = 'timestamp';
                }
            }
        }

        return $properties;
    }

    /**
     * Checks if the provided type or list of types contains the 'timestamp' type.
     *
     * @param string|array $type Type or list of types to check.
     *
     * @return bool `true` if 'timestamp' is found, `false` otherwise.
     *
     * @phpstan-param list<string>|string $type
     */
    private function containsTimestampType(string|array $type): bool
    {
        return is_string($type) ? $type === 'timestamp' : in_array('timestamp', $type, true);
    }

    /**
     * Checks if the class has a declared property with the given name.
     *
     * @param string $property Name of the property to check.
     *
     * @return bool `true` if the property is declared in the class, `false` otherwise.
     */
    private function hasDeclaredProperty(string $property): bool
    {
        return $this->reflection->hasProperty($property);
    }

    /**
     * Checks if the given property has the `DoNotCollect` attribute.
     *
     * @param ReflectionProperty $property Property to check.
     *
     * @return bool `true` if the property has the `DoNotCollect` attribute, `false` otherwise.
     */
    private function hasDoNotCollectAttribute(ReflectionProperty $property): bool
    {
        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === DoNotCollect::class) {
                return true;
            }
        }

        return false;
    }

    /**
     * Initializes properties with the `timestamp` type to the current time if they are null or zero.
     *
     * This method iterates through all properties and checks if they have the `timestamp` type.
     * - If a property has this type and its current value is null or zero, it sets the property's value to the current
     *   time using the `time()` function.
     */
    private function initializeTimestampProperties(): void
    {
        foreach ($this->properties as $property => $type) {
            if (!$this->containsTimestampType($type)) {
                continue;
            }

            $currentValue = $this->readProperty($property);

            if ($currentValue === null || $currentValue === 0) {
                $this->writeProperty($property, time());
            }
        }
    }

    /**
     * Performs type casting based on the expected type.
     *
     * @param string $expectedType Expected type to cast to.
     * @param mixed $value Value to be cast.
     *
     * @return mixed Value after type casting, or the original value if the expected type is not recognized.
     */
    private function performTypeCasting(string $expectedType, mixed $value): mixed
    {
        return match ($expectedType) {
            'bool' => (bool) $value,
            'float' => is_numeric($value) ? (float) $value : $value,
            'int' => is_numeric($value) ? (int) $value : $value,
            'string' => is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))
                ? (string) $value : $value,
            default => $value,
        };
    }

    /**
     * Reads the value of a property.
     *
     * @param string $property Name of the property to read.
     *
     * @throws InvalidArgumentException if the property is not defined in the model.
     *
     * @return mixed Value of the property.
     */
    private function readProperty(string $property): mixed
    {
        if (!array_key_exists($property, $this->properties)) {
            throw new InvalidArgumentException(
                Message::UNDEFINED_PROPERTY_WITH_CLASS->getMessage(
                    $this->model::class,
                    $property,
                ),
            );
        }

        if ($this->hasDeclaredProperty($property)) {
            $getter = static function (ModelInterface $class, string $property): mixed {
                /** @phpstan-ignore-next-line */
                return $class->$property;
            };

            $getter = Closure::bind($getter, null, $this->model);

            return $getter($this->model, $property);
        }

        return $this->dynamicValues[$property] ?? null;
    }

    /**
     * Resolves the type to be used for casting when multiple types are defined for a property.
     *
     * This method takes an array of expected types and returns the type to be used for casting.
     * - If there is exactly one non-null type in the array, that type is returned.
     * - Otherwise, null is returned, indicating that no specific type can be determined for casting.
     *
     * @return string|null Type to be used for casting, or null if no specific type can be determined.
     *
     * @phpstan-param list<string> $expectedTypes
     */
    private function resolveTypeForCasting(array $expectedTypes): string|null
    {
        $typesWithoutNull = array_values(
            array_filter(
                $expectedTypes,
                static fn(string $type): bool => $type !== 'null',
            ),
        );

        if (count($typesWithoutNull) !== 1) {
            return null;
        }

        return $typesWithoutNull[0];
    }

    /**
     * Sets the value of a property, supporting nested properties using dot notation.
     *
     * This method checks if the property exists and then sets its value.
     * - If the property is nested (indicated by dot notation), it traverses the nested properties to set the value at
     *   the correct level.
     *
     * @param string $property Name of the property to set, which can include dot notation for nested properties.
     * @param mixed $value Value to assign to the property.
     *
     * @throws InvalidArgumentException if the property does not exist in the model.
     */
    private function setPropertyValueInternal(string $property, mixed $value): void
    {
        if ($this->hasProperty($property) === false) {
            throw new InvalidArgumentException(
                Message::UNDEFINED_PROPERTY->getMessage($property),
            );
        }

        $properties = explode('.', $property);
        $propertyCount = count($properties);

        if ($propertyCount === 1) {
            $valueTypeCast = $this->phpTypeCast($property, $value);
            $this->writeProperty($property, $valueTypeCast);

            return;
        }

        /** @var int<0, max> $lastPropertyKey */
        $lastPropertyKey = array_key_last($properties);
        $lastProperty = $properties[$lastPropertyKey];

        $nestedProperty = array_slice($properties, 0, $propertyCount - 1);
        $nestedValue = $this->model->getPropertyValue(implode('.', $nestedProperty));

        if ($nestedValue instanceof ModelInterface) {
            $nestedValue->setPropertyValue($lastProperty, $value);
        }
    }

    /**
     * Convert a snake_case formatted string to camelCase.
     *
     * @param string $snakeCaseString snake_case formatted string to convert.
     *
     * @return string Converted camelCase string.
     */
    private function snakeCaseToCamelCase(string $snakeCaseString): string
    {
        $words = explode('_', $snakeCaseString);
        $camelCase = '';

        foreach ($words as $index => $word) {
            if ($index === 0) {
                $camelCase = $word;
            } else {
                $camelCase .= ucfirst($word);
            }
        }

        return $camelCase;
    }

    /**
     * Splits a property name into the current property and the nested property if dot notation is used.
     *
     * @return array An array containing the current property and the nested property (or `null` if not applicable).
     *
     * @phpstan-return list{0: string, 1: null|string}
     */
    private function splitProperty(string $property): array
    {
        if (str_contains($property, '.')) {
            $result = explode('.', $property, 2);

            return [$result[0], $result[1] ?? null];
        }

        return [$property, null];
    }

    /**
     * Writes a value to a property, supporting both declared and dynamic properties.
     *
     * This method checks if the property is declared in the class. If it is, it uses a closure to set the value
     * directly on the model instance.
     * - If the property is not declared, it stores the value in the `$dynamicValues` array.
     *
     * @param string $property Name of the property to write to.
     * @param mixed $value Value to assign to the property.
     */
    private function writeProperty(string $property, mixed $value): void
    {
        if ($this->hasDeclaredProperty($property) === false) {
            $this->dynamicValues[$property] = $value;
        } else {
            $setter = static function (ModelInterface $class, string $property, mixed $value): void {
                /** @phpstan-ignore-next-line */
                $class->$property = $value;
            };

            $setter = Closure::bind($setter, null, $this->model);

            $setter($this->model, $property, $value);
        }
    }
}
