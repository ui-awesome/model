<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;

final class Address extends AbstractModel
{
    public string $street = '';
    public string $city = '';

    public function __construct(public readonly Country $country) {}
}
