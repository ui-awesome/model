<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\{Cast, DefaultValue, DoNotCollect, MapFrom, NoSnakeCase, Timestamp, Trim};
use UIAwesome\Model\BaseModel;
use UIAwesome\Model\Tests\Support\Caster\SuffixCaster;

/**
 * Model with assignment and serialization metadata for clone regression tests.
 */
final class CloneState extends BaseModel
{
    #[Cast(SuffixCaster::class)]
    public string $castValue = '';
    #[DefaultValue('fallback')]
    public string $defaultValue = '';
    #[MapFrom('display-name')]
    #[NoSnakeCase]
    #[Trim]
    public string $displayName = '';
    #[DoNotCollect]
    public string $ignored = 'Uncollected';
    #[Timestamp]
    public int $updatedAt = 0;
}
