<?php

declare(strict_types=1);

namespace UIAwesome\Model;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

use function array_key_exists;
use function array_keys;
use function array_slice;
use function count;
use function explode;
use function get_class;
use function implode;
use function in_array;
use function is_array;
use function str_contains;

final class TypeCollector
{
    /**
     * @psalm-var array<string, list<string>|string>
     */
    private array $properties = [];

    public function __construct(private readonly ModelInterface $model)
    {
        $this->properties = $this->collectProperties();
    }

    /**
     * Adds a new property to the list of properties types.
     *
     * @param string $property The name of the property.
     * @param array|string $type The type of the property.
     *
     * @psalm-param list<string>|string $type
     */
    public function addProperty(string $property, string|array $type): void
    {
        $this->properties[$property] = $type;
    }

    /**
     * @return array The list of properties types indexed by property name.
     *
     * @psalm-return array<string, list<string>|string>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getPropertyValue(string $property): mixed
    {
        [$currentProperty, $nestedProperty] = $this->splitProperty($property);

        if ($this->isPropertyType($currentProperty, 'timestamp')) {
            $this->writeProperty($currentProperty, time());
        }

        $currentPropertyValue = $this->readProperty($currentProperty);

        if ($currentPropertyValue === null) {
            return null;
        }

        if ($nestedProperty !== null && $currentPropertyValue instanceof ModelInterface) {
            return $currentPropertyValue->getPropertyValue($nestedProperty);
        }

        return $currentPropertyValue;
    }

    public function hasProperty(string $property): bool
    {
        [$property, $nested] = $this->splitProperty($property);

        return $nested !== null || array_key_exists($property, $this->getProperties());
    }

    public function isPropertyType(string $property, string $type): bool
    {
        $propertyTypes = $this->properties[$property] ?? [];

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
        if ($this->model->hasProperty($property) === false) {
            return null;
        }

        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        $expectedTypes = $this->properties[$property];

        if (is_array($expectedTypes)) {
            return $value;
        }

        return $this->performTypeCasting($expectedTypes, $value);
    }

    public function setPropertyValue(string $property, mixed $value): void
    {
        if ($this->hasProperty($property) === false) {
            throw new InvalidArgumentException("Undefined property: \"$property\".");
        }

        $properties = explode('.', $property);
        $propertyCount = count($properties);

        if ($propertyCount === 1) {
            /** @psalm-var mixed $valueTypeCast */
            $valueTypeCast = $this->phpTypeCast($property, $value);
            $this->writeProperty($property, $valueTypeCast);

            return;
        }

        $lastProperty = $properties[$propertyCount - 1];
        $nestedProperty = array_slice($properties, 0, $propertyCount - 1);

        /** @psalm-var mixed $nestedValue */
        $nestedValue = $this->model->getPropertyValue(implode('.', $nestedProperty));

        if ($nestedValue instanceof ModelInterface) {
            $nestedValue->setPropertyValue($lastProperty, $value);
        }
    }

    public function setPropertiesValues(array $data, array $exceptPropierties = []): void
    {
        /**
         * @psalm-var array<string, mixed> $data
         * @psalm-var mixed $value
         */
        foreach ($data as $property => $value) {
            if (!in_array($property, $exceptPropierties, true)) {
                $camelCaseName = $this->snakeCaseToCamelCase($property);

                $this->setPropertyValue($camelCaseName, $value);
            }
        }
    }

    public function toArray(bool $snakeCase, array $exceptPropierties = []): array
    {
        /** @psalm-var string[] $properties */
        $properties = array_keys($this->getProperties());
        $result = [];

        foreach ($properties as $property) {
            if (!in_array($property, $exceptPropierties, true)) {
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
        $snakeCase = preg_replace('/([A-Z])/', '_$1', $value);

        return strtolower($snakeCase);
    }

    /**
     * Returns the list of property types indexed by property names.
     *
     * By default, this method returns all non-static properties of the class.
     *
     * @return array The list of property types indexed by property names.
     *
     * @psalm-return array<string, list<string>|string>
     */
    private function collectProperties(): array
    {
        $class = new ReflectionClass($this->model);
        $properties = [];

        foreach ($class->getProperties() as $property) {
            if ($property->isStatic() === false && !$this->hasDoNotCollectAttribute($property)) {
                /** @var ReflectionNamedType|ReflectionUnionType|null $type */
                $type = $property->getType();

                if ($type !== null) {
                    if ($type instanceof ReflectionUnionType) {
                        $typeNames = [];

                        foreach ($type->getTypes() as $unionType) {
                            $typeNames[] = $unionType->getName();
                        }

                        $properties[$property->getName()] = $typeNames;
                    } else {
                        $properties[$property->getName()] = $type->getName();
                    }
                } else {
                    $properties[$property->getName()] = '';
                }

                foreach ($property->getAttributes(Attribute\Timestamp::class) as $ignored) {
                    $properties[$property->getName()] = 'timestamp';
                }
            }
        }

        return $properties;
    }

    private function hasDoNotCollectAttribute(ReflectionProperty $property): bool
    {
        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === Attribute\DoNotCollect::class) {
                return true;
            }
        }

        return false;
    }

    private function performTypeCasting(string $expectedType, mixed $value): mixed
    {
        return match ($expectedType) {
            'bool' => (bool) $value,
            'float' => (float) $value,
            'int' => (int) $value,
            'string' => (string) $value,
            default => $value,
        };
    }

    private function readProperty(string $property): mixed
    {
        [$property, $nested] = $this->splitProperty($property);

        if ($this->hasProperty($property) === false) {
            throw new InvalidArgumentException(
                'Undefined property: "' . get_class($this->model) . '::' . $property . '".'
            );
        }

        $getter = static fn(ModelInterface $class, string $property): mixed => $class->$property;
        $getter = Closure::bind($getter, null, $this->model);

        /** @psalm-var Closure $getter */
        return $getter($this->model, $property, $nested);
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
        if (str_contains($snakeCaseString, '_') === false) {
            return $snakeCaseString;
        }

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
     * @psalm-return list{0: string, 1: null|string}
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
        [$property, $nested] = $this->splitProperty($property);

        $setter = static function (ModelInterface $class, string $property, mixed $value): void {
            $class->$property = $value;
        };
        $setter = Closure::bind($setter, null, $this->model);

        /** @psalm-var Closure $setter */
        $setter($this->model, $property, $value, $nested);
    }
}
