<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;

final class PropertyType extends AbstractModel
{
    public string $name = '';
    private array $array = [];
    private bool $bool = false;
    private float $float = 0;
    private int $int = 0;
    private int|null $nullable = null;
    private object|null $object = null;
    private string $string = '';
    private $withoutType = null;
}
