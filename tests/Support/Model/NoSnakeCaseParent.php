<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\{DoNotCollect, NoSnakeCase};
use UIAwesome\Model\BaseModel;

/**
 * Parent stub model combining DoNotCollect and NoSnakeCase metadata for tests.
 */
class NoSnakeCaseParent extends BaseModel
{
    #[DoNotCollect]
    #[NoSnakeCase]
    private string $apiVersion = '';
}
