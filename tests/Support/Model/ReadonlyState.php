<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;

/**
 * Stub model exposing readonly properties for assignment tests.
 */
final class ReadonlyState extends BaseModel
{
    /**
     * @phpstan-ignore property.uninitializedReadonly (Assigned through the model API, never in a constructor.)
     */
    public readonly string $token;

    public function __construct(public readonly Country $country) {}
}
