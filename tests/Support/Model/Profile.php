<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\DoNotCollect;

final class Profile extends AbstractModel
{
    #[DoNotCollect]
    public array $avatar = [];
    public string $bio = '';
    public string $pathAvatar = '';
    public string $publicEmailPersonal = '';

    public function __construct(public readonly Address $address) {}
}
