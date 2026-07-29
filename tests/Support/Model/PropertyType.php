<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;

/**
 * Stub model declaring scalar, nullable, and untyped properties for tests.
 */
final class PropertyType extends BaseModel
{
    public string $name = '';
    /**
     * @var array<array-key, mixed>
     */
    private array $array = [];
    /**
     * @phpstan-ignore property.tooWideBool (Assigned through the model API, never in this class.)
     */
    private bool $bool = false;
    private float $float = 0;
    private int $int = 0;
    /**
     * @phpstan-ignore property.unusedType (Assigned through the model API, never in this class.)
     */
    private int|null $nullable = null;
    /**
     * @phpstan-ignore property.unusedType (Assigned through the model API, never in this class.)
     */
    private object|null $object = null;
    private string $string = '';
    /**
     * @var mixed
     */
    private $withoutType = null;

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return [
            'name' => $this->name,
            'array' => $this->array,
            'bool' => $this->bool,
            'float' => $this->float,
            'int' => $this->int,
            'nullable' => $this->nullable,
            'object' => $this->object,
            'string' => $this->string,
            'withoutType' => $this->withoutType,
        ];
    }
}
