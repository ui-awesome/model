<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;

/**
 * Stub nested address model used by test fixtures.
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class Address extends AbstractModel
{
    public string $city = '';
    public string $street = '';

    public function __construct(public readonly Country $country) {}
}
