<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;

/**
 * Stub nested address model used for tests.
 */
final class Address extends BaseModel
{
    public string $city = '';

    public string $street = '';

    public function __construct(public readonly Country $country) {}
}
