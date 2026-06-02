<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\NoSnakeCase;
use UIAwesome\Model\BaseModel;

/**
 * Stub model containing NoSnakeCase-marked properties used for tests.
 */
final class NoSnakeCasePayload extends BaseModel
{
    #[NoSnakeCase]
    public string $apiVersion = '';

    public string $publicEmailPersonal = '';
}
