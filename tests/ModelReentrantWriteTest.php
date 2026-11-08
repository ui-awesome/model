<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use InvalidArgumentException;
use Override;
use PHPUnit\Framework\Attributes\{DataProviderExternal, Group};
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\Attribute\DoNotCollect;
use UIAwesome\Model\BaseModel;
use UIAwesome\Model\Exception\Message;
use UIAwesome\Model\Tests\Provider\ModelReentrantWriteProvider;
use UIAwesome\Model\Tests\Support\Model\{Dynamic, ReentrantWrite};

/**
 * Tests for reentrant collector writes, exception cleanup and cloned write scopes.
 *
 * {@see ModelReentrantWriteProvider} supplies the affected write entry points.
 */
#[Group('model')]
final class ModelReentrantWriteTest extends TestCase
{
    public function testCloneStartsItsOwnWriteScope(): void
    {
        $nested = new Dynamic();

        $nested->add('city', 'string');

        $original = new class ($nested) extends BaseModel {
            #[DoNotCollect]
            public self|null $copy = null;

            public function __construct(public Dynamic $nested) {}

            #[Override]
            public function getValue(string $property): mixed
            {
                if ($property === 'nested') {
                    $copy = clone $this;

                    $copy->setValue('count', 7);

                    $this->copy = $copy;
                }

                return parent::getValue($property);
            }
        };

        $original->add('count', 'int');
        $original->setValue('count', 1);
        $original->setValue('nested.city', 'City');

        $copy = $original->copy;

        self::assertNotNull(
            $copy,
            'The getter must create a clone during the active write.',
        );
        self::assertSame(
            1,
            $original->getValue('count'),
            'An active-scope clone must not write to the source collector.',
        );

        $snapshot = clone $copy;

        $copy->setValue('count', 8);

        self::assertSame(
            7,
            $snapshot->getValue('count'),
            'The clone must not inherit the original active write depth.',
        );
        self::assertSame(
            8,
            $copy->getValue('count'),
            'The active-scope clone must remain independently writable.',
        );
    }
    #[DataProviderExternal(ModelReentrantWriteProvider::class, 'bulkEntryPoints')]
    public function testPreserveReentrantWrites(bool $scoped): void
    {
        $nested = new Dynamic();

        $nested->add('city', 'string');

        $original = new ReentrantWrite($nested);

        $original->add('count', 'int');
        $original->add('updatedAt', 'timestamp');
        $original->setValue('count', 7);

        $copy = clone $original;

        $data = [
            'nested.city' => 'City',
            'count' => '8',
        ];

        if ($scoped) {
            self::assertTrue(
                $copy->load(['edit' => $data], 'edit'),
                'Scoped reentrant loading must succeed.',
            );
        } else {
            $copy->setValues($data);
        }

        self::assertSame(
            8,
            $copy->getValue('count'),
            'The outer assignment must follow the reentrant bulk write.',
        );
        self::assertSame(
            'Read',
            $copy->getValue('observed'),
            'Reentrant registrations and setter values must survive.',
        );
        self::assertSame(
            'City',
            $copy->getValue('nested.city'),
            'The nested property must still be assigned.',
        );
        self::assertGreaterThan(
            0,
            $copy->getValue('updatedAt'),
            'Timestamp state must remain available.',
        );
        self::assertSame(
            7,
            $original->getValue('count'),
            'Reentrant clone writes must not alter original dynamic values.',
        );
        self::assertFalse(
            $original->has('observed'),
            'Reentrant registrations must not reach the original.',
        );
        self::assertSame(
            ['count' => 8, 'observed' => 'Read'],
            $copy->toArray(false, ['nested', 'updatedAt']),
            'Serialization must retain outer and reentrant values.',
        );

        $snapshot = clone $copy;

        $copy->setValue('count', 9);

        self::assertSame(
            8,
            $snapshot->getValue('count'),
            'Completed reentrant writes must not keep a scope active.',
        );
    }

    #[DataProviderExternal(ModelReentrantWriteProvider::class, 'failingEntryPoints')]
    public function testReleaseWriteScopeAfterException(bool $bulk): void
    {
        $original = new Dynamic();

        $original->add('count', 'int');
        $original->setValue('count', 7);

        try {
            if ($bulk) {
                $original->setValues(['missing' => true]);
            } else {
                $original->setValue('missing', true);
            }

            self::fail(
                'Undefined assignments must throw before scope cleanup is checked.',
            );
        } catch (InvalidArgumentException $exception) {
            self::assertSame(
                Message::UNDEFINED_PROPERTY->getMessage('missing'),
                $exception->getMessage(),
                'The exception message must match the expected undefined property message.',
            );
        }

        $copy = clone $original;

        $original->setValue('count', 8);

        self::assertSame(
            7,
            $copy->getValue('count'),
            'A failed write must not disable later clone isolation.',
        );
        self::assertSame(
            8,
            $original->getValue('count'),
            'The original must remain writable after an exception.',
        );
    }
}
