<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use Override;
use UIAwesome\Model\BaseModel;

use function is_string;

/**
 * Stub model overriding getValue to validate toArray behavior.
 */
final class GetValueOverride extends BaseModel
{
    public string $name = 'ada';

    #[Override]
    public function getValue(string $property): mixed
    {
        $value = parent::getValue($property);

        return is_string($value) ? $value . '-override' : $value;
    }
}
