<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\{Cast, MapFrom, Trim};
use UIAwesome\Model\BaseModel;
use UIAwesome\Model\Tests\Support\Caster\PipeSeparatedCaster;

/**
 * Stub model with cast-enabled properties for tests.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class CastPayload extends BaseModel
{
    #[Cast(PipeSeparatedCaster::class)]
    public array $keywords = [];

    public string $name = '';

    #[Trim]
    #[MapFrom('tag-list')]
    #[Cast('array')]
    public array $tagList = [];

    #[Cast('array')]
    public array $tags = [];
}
