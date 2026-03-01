<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;

/**
 * Stub model declaring scalar, nullable, and untyped properties for tests.
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class PropertyType extends AbstractModel
{
    private array $array = [];

    private bool $bool = false;

    private float $float = 0;

    private int $int = 0;

    public string $name = '';

    private int|null $nullable = null;

    private object|null $object = null;

    private string $string = '';

    private $withoutType = null;
}
