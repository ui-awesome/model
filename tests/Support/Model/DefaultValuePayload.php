<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\{Cast, DefaultValue, DoNotCollect, MapFrom, Trim};

/**
 * Stub model with DefaultValue-driven properties for tests.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class DefaultValuePayload extends AbstractModel
{
    #[DefaultValue('Guest')]
    public string $displayName = '';

    #[Trim]
    #[DefaultValue('Unknown')]
    public string $bio = '';

    #[MapFrom('user-locale')]
    #[DefaultValue('en_US')]
    public string $locale = '';

    #[DefaultValue('draft')]
    public string|null $status = null;

    #[Cast('array')]
    #[DefaultValue('php,model')]
    public array $tags = [];

    #[DoNotCollect]
    #[DefaultValue('ignored')]
    public string $internal = '';
}
