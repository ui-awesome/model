<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\Attribute\MapFrom;
use UIAwesome\Model\BaseModel;

/**
 * Stub model with explicit input-key mappings used for tests.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class MapFromPayload extends BaseModel
{
    #[MapFrom('@context')]
    public string $context = '';

    public string $publicEmailPersonal = '';

    #[MapFrom('user-email-address')]
    public string $userEmailAddress = '';
}
