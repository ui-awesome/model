<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\{Cast, DefaultValue, DoNotCollect, MapFrom, Trim};
use UIAwesome\Model\BaseModel;

/**
 * Stub model with DefaultValue-driven properties for tests.
 */
final class DefaultValuePayload extends BaseModel
{
    #[Trim]
    #[DefaultValue('Unknown')]
    public string $bio = '';

    #[DefaultValue('Guest')]
    public string $displayName = '';

    #[DoNotCollect]
    #[DefaultValue('ignored')]
    public string $internal = '';

    #[MapFrom('user-locale')]
    #[DefaultValue('en_US')]
    public string $locale = '';

    #[DefaultValue('draft')]
    public string|null $status = null;

    #[Cast('array')]
    #[DefaultValue('php,model')]
    public array $tags = [];
}
