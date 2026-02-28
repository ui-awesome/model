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
     * @param string $property The name of the property.
     * @param array|string $type The type of the property.
     *
     * @phpstan-param list<string>|string $type
     */
    public function addProperty(string $property, string|array $type): void
    {
        $this->properties[$property] = $type;
    }

    /**
     * @return array The list of properties types indexed by property name.
     *
     * @phpstan-return array<string, list<string>|string>
     */
    public function getPropertyTypes(): array
    {
        return $this->properties;
    }

    public function getPropertyValue(string $property): mixed
    {
        [$currentProperty, $nestedProperty] = $this->splitProperty($property);

        $currentPropertyValue = $this->readProperty($currentProperty);

        if ($nestedProperty !== null && $currentPropertyValue instanceof ModelInterface) {
            return $currentPropertyValue->getPropertyValue($nestedProperty);
        }

        return $currentPropertyValue;
    }

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

    public function isPropertyType(string $property, string $type): bool
    {
        $propertyTypes = $this->properties[$property] ?? '';

        return is_string($propertyTypes) ? $propertyTypes === $type : in_array($type, $propertyTypes, true);
    }

    /**
     * Converts the value of an property to the type specified by type hinting in the PHPDoc.
     *
     * @param string $property The property name.
     * @param mixed $value The value to be converted.
     *
     * @return mixed The value of the property converted to the type specified by PHPDoc.
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
     * @phpstan-param mixed[] $data
     * @phpstan-param mixed[] $exceptProperties Camel-case property names to exclude.
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

    public function setPropertyValue(string $property, mixed $value): void
    {
        $this->setPropertyValueInternal($property, $value);
        $this->initializeTimestampProperties();
    }

    /**
     * @phpstan-param mixed[] $exceptProperties
     *
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
     * @param string $value The camelCase formatted string to convert.
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
     * @return array The list of property types indexed by property names.
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
     * @phpstan-param list<string>|string $type
     */
    private function containsTimestampType(string|array $type): bool
    {
        return is_string($type) ? $type === 'timestamp' : in_array('timestamp', $type, true);
    }

    private function hasDeclaredProperty(string $property): bool
    {
        return $this->reflection->hasProperty($property);
    }

    private function hasDoNotCollectAttribute(ReflectionProperty $property): bool
    {
        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === DoNotCollect::class) {
                return true;
            }
        }

        return false;
    }

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

    private function performTypeCasting(string $expectedType, mixed $value): mixed
    {
        return match ($expectedType) {
            'bool' => (bool) $value,
            'float' => is_numeric($value) ? (float) $value : $value,
            'int' => is_numeric($value) ? (int) $value : $value,
            'string' => is_scalar($value) || (is_object($value) && method_exists($value, '__toString')) ? (string) $value : $value,
            default => $value,
        };
    }

    private function readProperty(string $property): mixed
    {
        if (!array_key_exists($property, $this->properties)) {
            throw new InvalidArgumentException(
                'Undefined property: "' . $this->model::class . '::' . $property . '".',
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
     * @phpstan-param list<string> $expectedTypes
     */
    private function resolveTypeForCasting(array $expectedTypes): string|null
    {
        $typesWithoutNull = array_values(array_filter($expectedTypes, static fn(string $type): bool => $type !== 'null'));

        if (count($typesWithoutNull) !== 1) {
            return null;
        }

        return $typesWithoutNull[0];
    }

    private function setPropertyValueInternal(
        string $property,
        mixed $value,
    ): void {
        if ($this->hasProperty($property) === false) {
            throw new InvalidArgumentException("Undefined property: \"$property\".");
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
     * @param string $snakeCaseString The snake_case formatted string to convert.
     *
     * @return string The converted camelCase string.
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
