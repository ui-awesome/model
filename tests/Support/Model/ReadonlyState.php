<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use UIAwesome\Model\BaseModel;

/**
 * Stub model exposing readonly properties for assignment tests.
 */
final class ReadonlyState extends BaseModel
{
    public readonly string $token;

    public function __construct(public readonly Country $country) {}
}
