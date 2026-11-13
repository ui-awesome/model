<?php

declare(strict_types=1);

namespace UIAwesome\Model;

use PHPForge\Helper\Reflector;

use function array_filter;
use function array_values;
use function count;
use function is_a;
use function is_array;
use function is_string;
use function iterator_to_array;

/**
 * Base implementation of {@see ModelInterface}.
 */
abstract class BaseModel implements ModelInterface
{
    /**
     * Keeps reentrant assignments on the collector selected for the active outer write.
     */
    private int $collectorWriteDepth = 0;
    /**
     * Stores raw input retained after loading.
     *
     * @phpstan-var mixed[]
     */
    private array $data = [];
    /**
     * Lazily initialized collector handling property metadata and value assignment.
     */
    private TypeCollector|null $typeCollector = null;

    /**
     * Registers a property and its supported types.
     */
    public function add(string $property, string|array $type): void
    {
        $this->typeCollector(true)->add($property, $type);
    }

    /**
     * Returns the raw input retained after loading.
     *
     * @phpstan-return mixed[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Returns the model class name used as the default input scope.
     */
    public function getModelName(): string
    {
        return Reflector::shortName(static::class);
    }

    /**
     * Returns property names, expanding nested models with dot notation.
     *
     * @phpstan-return list<string>
     */
    public function getNames(): array
    {
        return $this->getNestedNames($this, '');
    }

    /**
     * Returns collected property types indexed by property name.
     *
     * @phpstan-return array<string, list<string>|string>
     */
    public function getTypes(): array
    {
        return $this->typeCollector()->getTypes();
    }

    /**
     * Returns a property value, including nested model paths.
     */
    public function getValue(string $property): mixed
    {
        return $this->typeCollector()->getValue($property);
    }

    /**
     * Checks whether a property or nested model path exists.
     */
    public function has(string $property): bool
    {
        return $this->typeCollector()->has($property);
    }

    /**
     * Checks whether the loaded raw input is empty.
     */
    public function isEmpty(): bool
    {
        return $this->data === [];
    }

    /**
     * Checks whether a property supports the requested type.
     */
    public function isType(string $property, string $type): bool
    {
        return $this->typeCollector()->isType($property, $type);
    }

    /**
     * Assigns scoped or unscoped input and retains its raw keys.
     */
    public function load(iterable $data, string|null $modelName = null): bool
    {
        $this->data = [];

        $scope = $modelName ?? $this->getModelName();
        $sourceData = is_array($data) ? $data : iterator_to_array($data);

        /** @phpstan-var array<string, mixed> $rawData */
        $rawData = isset($sourceData[$scope]) && is_array($sourceData[$scope]) ? $sourceData[$scope] : $sourceData;

        $this->setValues($rawData);

        $this->data = $rawData;

        return $rawData !== [];
    }

    /**
     * Assigns a property value using its attributes and type metadata.
     */
    public function setValue(string $property, mixed $value): void
    {
        $collector = $this->typeCollector(true);
        ++$this->collectorWriteDepth;

        try {
            $collector->setValue($property, $value);
        } finally {
            --$this->collectorWriteDepth;
        }
    }

    /**
     * Assigns input values while excluding the selected property names.
     *
     * @phpstan-param array<array-key, mixed> $data
     * @phpstan-param list<string> $except
     */
    public function setValues(array $data, array $except = []): void
    {
        $collector = $this->typeCollector(true);
        ++$this->collectorWriteDepth;

        try {
            $collector->setValues($data, $except);
        } finally {
            --$this->collectorWriteDepth;
        }
    }

    /**
     * Exports property values with optional top-level snake_case keys.
     *
     * @phpstan-param list<string> $exceptProperties
     * @phpstan-return array<string, mixed>
     */
    public function toArray(bool $snakeCase = false, array $exceptProperties = []): array
    {
        return $this->typeCollector()->toArray($snakeCase, $exceptProperties);
    }

    /**
     * Recursively collects property names, flattening nested models with dot notation.
     *
     * @param ModelInterface $model Model instance to inspect.
     * @param string $prefix Prefix accumulated for nested paths.
     *
     * @return array List of property names, including nested properties in dot notation.
     *
     * @phpstan-return list<string>
     */
    private function getNestedNames(ModelInterface $model, string $prefix): array
    {
        $properties = [];

        foreach ($model->getTypes() as $property => $type) {
            $types = is_array($type)
                ? array_values(array_filter($type, static fn(mixed $type): bool => $type !== 'null'))
                : [$type];

            if (
                is_string($property)
                && count($types) === 1
                && is_string($types[0])
                && is_a($types[0], ModelInterface::class, true)
            ) {
                $nestedModel = $model->getValue($property);

                if ($nestedModel instanceof ModelInterface) {
                    $nestedNames = $this->getNestedNames($nestedModel, $prefix . $property . '.');
                    $properties = [...$properties, ...$nestedNames];
                    continue;
                }
            }

            $properties[] = $prefix . $property;
        }

        return $properties;
    }

    /**
     * Returns a collector bound to this model, initializing or copying its state when needed.
     *
     * Copying before outer writes isolates clones regardless of custom clone hooks. Reentrant writes share the active
     * collector, while rebinding a clone clears any inherited write depth.
     *
     * @return TypeCollector Collector owned by the receiving model.
     */
    private function typeCollector(bool $forWrite = false): TypeCollector
    {
        if ($this->typeCollector === null) {
            $this->typeCollector = new TypeCollector($this);
        } elseif (!$this->typeCollector->isForModel($this) || ($forWrite && $this->collectorWriteDepth === 0)) {
            $this->typeCollector = $this->typeCollector->withModel($this);
            $this->collectorWriteDepth = 0;
        }

        return $this->typeCollector;
    }
}
