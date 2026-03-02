<?php

declare(strict_types=1);

namespace UIAwesome\Model;

use function class_exists;
use function is_array;
use function iterator_to_array;
use function str_contains;
use function strrchr;
use function substr;

/**
 * Base implementation of {@see ModelInterface}.
 *
 * Usage example:
 * ```php
 * final class UserForm extends AbstractModel
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
abstract class AbstractModel implements ModelInterface
{
    /**
     * @phpstan-var mixed[]
     */
    private array $data = [];

    /**
     * Lazily initialized collector handling property metadata and value assignment.
     */
    private TypeCollector|null $typeCollector = null;

    public function add(string $property, string|array $type): void
    {
        $this->typeCollector()->add($property, $type);
    }

    /**
     * @phpstan-return mixed[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getModelName(): string
    {
        if (str_contains(static::class, '@anonymous')) {
            return '';
        }

        $className = strrchr(static::class, '\\');

        if ($className === false) {
            return static::class;
        }

        return substr($className, 1);
    }

    /**
     * @phpstan-return list<string>
     */
    public function getNames(): array
    {
        return $this->getNestedNames($this, '');
    }

    /**
     * @phpstan-return array<string, list<string>|string>
     */
    public function getTypes(): array
    {
        return $this->typeCollector()->getTypes();
    }

    public function getValue(string $property): mixed
    {
        return $this->typeCollector()->getValue($property);
    }

    public function has(string $property): bool
    {
        return $this->typeCollector()->has($property);
    }

    public function isEmpty(): bool
    {
        return $this->data === [];
    }

    public function isType(string $property, string $type): bool
    {
        return $this->typeCollector()->isType($property, $type);
    }

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

    public function setValue(string $property, mixed $value): void
    {
        $this->typeCollector()->setValue($property, $value);
    }

    /**
     * @phpstan-param array<array-key, mixed> $data
     * @phpstan-param list<string> $except
     */
    public function setValues(array $data, array $except = []): void
    {
        $this->typeCollector()->setValues($data, $except);
    }

    /**
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
            if (is_string($property) && is_string($type) && class_exists($type)) {
                $nestedModel = $model->getValue($property);

                if ($nestedModel instanceof ModelInterface) {
                    $nestedNames = $this->getNestedNames($nestedModel, $prefix . $property . '.');
                    $properties = [...$properties, ...$nestedNames];
                }
            } else {
                $properties[] = $prefix . $property;
            }
        }

        return $properties;
    }

    /**
     * Returns the model type collector, initializing it on first access.
     *
     * @return TypeCollector Shared collector instance for the model.
     */
    private function typeCollector(): TypeCollector
    {
        if ($this->typeCollector === null) {
            $this->typeCollector = new TypeCollector($this);
        }

        return $this->typeCollector;
    }
}
