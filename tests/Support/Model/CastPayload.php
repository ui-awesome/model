<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\{Cast, MapFrom, Trim};
use UIAwesome\Model\BaseModel;
use UIAwesome\Model\Tests\Support\Caster\PipeSeparatedCaster;

/**
 * Stub model with cast-enabled properties for tests.
 */
final class CastPayload extends BaseModel
{
    /**
     * @var array<int, string>
     */
    #[Cast(PipeSeparatedCaster::class)]
    public array $keywords = [];
    public string $name = '';
    /**
     * @var array<int, string>
     */
    #[Trim]
    #[MapFrom('tag-list')]
    #[Cast('array')]
    public array $tagList = [];
    /**
     * @var array<int, mixed>
     */
    #[Cast('array')]
    public array $tags = [];
}
