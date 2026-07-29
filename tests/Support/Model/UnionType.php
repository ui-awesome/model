<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;

/**
 * Stub model with union-typed property used for tests.
 */
final class UnionType extends BaseModel
{
    /**
     * @phpstan-ignore property.unusedType, property.unusedType, property.unusedType, property.unusedType
     */
    private bool|int|object|string|null $union = null;

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return [
            'union' => $this->union,
        ];
    }
}
