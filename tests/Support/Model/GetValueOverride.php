<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;

use function is_string;

/**
 * Stub model overriding getValue to validate toArray behavior.
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class GetValueOverride extends AbstractModel
{
    public string $name = 'ada';

    public function getValue(string $property): mixed
    {
        $value = parent::getValue($property);

        return is_string($value) ? $value . '-override' : $value;
    }
}
