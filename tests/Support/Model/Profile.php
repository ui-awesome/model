<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\DoNotCollect;
use UIAwesome\Model\BaseModel;

/**
 * Stub profile model with nested address relation used for tests.
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class Profile extends BaseModel
{
    #[DoNotCollect]
    public array $avatar = [];

    public string $bio = '';

    public string $pathAvatar = '';

    public string $publicEmailPersonal = '';

    public function __construct(public readonly Address $address) {}
}
