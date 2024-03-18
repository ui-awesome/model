<?php

declare(strict_types=1);

namespace UIAwesome\Model;

use function class_exists;
use function str_contains;
use function strrchr;
use function substr;

abstract class AbstractModel implements ModelInterface
{
    private array $data = [];
    private TypeCollector|null $typeCollector = null;

    public function addProperty(string $property, string|array $type): void
    {
        $this->typeCollector()->addProperty($property, $type);
    }

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

    public function getProperties(): array
    {
        return $this->getNestedProperties($this, '');
    }

    /**
     * @psalm-return array<string, list<string>|string>
     */
    public function getPropertiesTypes(): array
    {
        return $this->typeCollector()->getProperties();
    }

    public function getPropertyValue(string $property): mixed
    {
        return $this->typeCollector()->getPropertyValue($property);
    }

    public function hasProperty(string $property): bool
    {
        return $this->typeCollector()->hasProperty($property);
    }

    public function isEmpty(): bool
    {
        return $this->data === [];
    }

    public function isPropertyType(string $property, string $type): bool
    {
        return $this->typeCollector()->isPropertyType($property, $type);
    }

    public function load(iterable $data, string $modelName = null): bool
    {
        $this->data = [];
        $scope = $modelName ?? $this->getModelName();

        /** @psalm-var array<string,string> $rawData */
        $rawData = match (isset($data[$scope])) {
            true => $data[$scope],
            false => $data,
        };

        $this->data = $rawData;

        foreach ($rawData as $property => $value) {
            $this->setPropertyValue($property, $value);
        }

        return $rawData !== [];
    }

    public function setPropertyValue(string $property, mixed $value): void
    {
        $this->typeCollector()->setPropertyValue($property, $value);
    }

    public function setPropertiesValues(array $data, array $exceptPropierties = []): void
    {
        $this->typeCollector()->setPropertiesValues($data, $exceptPropierties);
    }

    public function toArray(bool $snakeCase = false, array $exceptPropierties = []): array
    {
        return $this->typeCollector()->toArray($snakeCase, $exceptPropierties);
    }

    private function typeCollector(): TypeCollector
    {
        if ($this->typeCollector === null) {
            $this->typeCollector = new TypeCollector($this);
        }

        return $this->typeCollector;
    }

    /**
     * @psalm-return array<string>
     */
    private function getNestedProperties(ModelInterface $model, string $prefix): array
    {
        $properties = [];

        foreach ($model->getPropertiesTypes() as $property => $type) {
            if (is_string($type) && class_exists($type)) {
                $nestedModel = $model->$property;

                if ($nestedModel instanceof ModelInterface) {
                    $nestedProperty = $this->getNestedProperties($nestedModel, $prefix . $property . '.');
                    $properties = [...$properties, ...$nestedProperty];
                }
            } else {
                $properties[] = $prefix . $property;
            }
        }

        return $properties;
    }
}
