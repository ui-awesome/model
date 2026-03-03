<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;

/**
 * Stub nested address model used for tests.
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class Address extends BaseModel
{
    public string $city = '';

    public string $street = '';

    public function __construct(public readonly Country $country) {}
}
