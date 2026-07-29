<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;
use UIAwesome\Model\Tests\Support\Contract\{IntersectionLeft, IntersectionRight};

/**
 * Stub model with intersection-typed property used for tests.
 */
final class IntersectionType extends BaseModel
{
    /**
     * @phpstan-ignore property.uninitializedReadonly (Reflected for type metadata, never assigned.)
     */
    private readonly IntersectionLeft&IntersectionRight $intersection;

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        if (!isset($this->intersection)) {
            return [];
        }

        return [
            'intersection' => $this->intersection,
        ];
    }
}
