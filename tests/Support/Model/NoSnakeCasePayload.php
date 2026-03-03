<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\NoSnakeCase;
use UIAwesome\Model\BaseModel;

/**
 * Stub model containing NoSnakeCase-marked properties used for tests.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class NoSnakeCasePayload extends BaseModel
{
    #[NoSnakeCase]
    public string $apiVersion = '';

    public string $publicEmailPersonal = '';
}
