<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\MapFrom;
use UIAwesome\Model\Attribute\Trim;

/**
 * Stub model with trim-enabled properties for tests.
 *
 * @copyright Copyright (C) 2026 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class TrimProfile extends AbstractModel
{
    #[MapFrom('display-name')]
    #[Trim]
    public string $displayName = '';
    #[Trim]
    public string $name = '';
    public string|null $nickname = null;
    public string $rawName = '';
}
