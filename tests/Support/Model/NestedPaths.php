<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;

/**
 * Stub model contrasting nullable, required and ambiguous nested paths.
 */
final class NestedPaths extends BaseModel
{
    public Address|null $address;
    public Address|Country|null $ambiguous;
    public string $name = 'Original';
    public Address $required;

    public function __construct()
    {
        $this->address = new Address(new Country());
        $this->required = new Address(new Country());
        $this->ambiguous = new Address(new Country());
    }
}
