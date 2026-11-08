<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests\Support\Model;

use Override;
use UIAwesome\Model\BaseModel;

/**
 * Nested model whose getter registers and writes dynamic values on the same receiver.
 */
final class ReentrantWrite extends BaseModel
{
    public function __construct(public Dynamic $nested) {}

    #[Override]
    public function getValue(string $property): mixed
    {
        if ($property === 'nested') {
            $this->add('observed', 'string');
            $this->setValue('observed', 'Read');
            $this->setValues(['count' => '3']);
        }

        return parent::getValue($property);
    }
}
