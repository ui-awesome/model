<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\MapFrom;
use UIAwesome\Model\Attribute\Trim;
use UIAwesome\Model\BaseModel;

/**
 * Stub model with trim-enabled properties for tests.
 */
final class TrimProfile extends BaseModel
{
    #[MapFrom('display-name')]
    #[Trim]
    public string $displayName = '';

    #[Trim]
    public string $name = '';

    public string|null $nickname = null;

    public string $rawName = '';
}
