<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;

final class Address extends AbstractModel
{
    public string $city = '';
    public string $street = '';

    public function __construct(public readonly Country $country) {}
}
