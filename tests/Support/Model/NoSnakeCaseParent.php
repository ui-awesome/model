<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\{DoNotCollect, NoSnakeCase};

/**
 * Parent stub model combining DoNotCollect and NoSnakeCase metadata.
 */
class NoSnakeCaseParent extends AbstractModel
{
    #[DoNotCollect]
    #[NoSnakeCase]
    private string $apiVersion = '';
}
