<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use PHPUnit\Framework\Attributes\{DataProviderExternal, Group};
use PHPUnit\Framework\TestCase;
use stdClass;
use UIAwesome\Model\Tests\Provider\ModelCloneProvider;
use UIAwesome\Model\Tests\Support\Model\{CloneHook, CloneState, Country, Dynamic, DynamicNested};

/**
 * Unit tests for collector ownership and model state preservation during cloning.
 *
 * {@see ModelCloneProvider} supplies cold and initialized collector states.
 */
#[Group('model')]
final class ModelCloneTest extends TestCase
{
    #[DataProviderExternal(ModelCloneProvider::class, 'collectorStates')]
    public function testLoadScopedDataOnCloneAndOriginalIndependently(Country|CloneHook $original, bool $initialized): void
    {
        $original->name = 'Original';

        if ($initialized) {
            $original->getTypes();
        }

        $copy = clone $original;

        self::assertTrue(
            $copy->load(['edit' => ['name' => 'Copy']], 'edit'),
            'Clone must load its explicit scope.',
        );
        self::assertSame(
            'Copy',
            $copy->name,
            'Scoped loading must write to the clone property.',
        );
        self::assertSame(
            'Copy',
            $copy->getValue('name'),
            'Scoped clone reads must match its direct property.',
        );
        self::assertSame(
            'Original',
            $original->getValue('name'),
            'Clone loading must not modify the original.',
        );
        self::assertSame(
            ['name' => 'Copy'],
            $copy->getData(),
            'Clone must retain its scoped payload.',
        );
        self::assertSame(
            [],
            $original->getData(),
            'Clone loading must not replace original load data.',
        );
        self::assertTrue(
            $original->load([$original->getModelName() => ['name' => 'Changed original']]),
            'Original scope must still load.',
        );
        self::assertSame(
            'Changed original',
            $original->name,
            'Original loading must write to the original.',
        );
        self::assertSame(
            ['name' => 'Copy'],
            $copy->toArray(),
            'Original loading must not change clone serialization.',
        );
        self::assertSame(
            ['name' => 'Copy'],
            $copy->getData(),
            'Original loading must not replace clone load data.',
        );
    }

    public function testPreserveAssignmentAndSerializationMetadataOnClone(): void
    {
        $original = new CloneState();

        $original->getTypes();

        $copy = clone $original;

        $copy->load(
            ['edit' => ['display-name' => ' Copy ', 'default_value' => '', 'cast_value' => 'Value']],
            'edit',
        );

        self::assertSame(
            'Copy',
            $copy->displayName,
            'Clone must retain MapFrom and Trim metadata.',
        );
        self::assertSame(
            'fallback',
            $copy->defaultValue,
            'Clone must retain runtime default metadata.',
        );
        self::assertSame(
            'Value-cast',
            $copy->castValue,
            'Clone must retain custom cast metadata.',
        );
        self::assertGreaterThan(
            0,
            $copy->updatedAt,
            'Clone must retain timestamp metadata for new assignments.',
        );
        self::assertSame(
            0,
            $original->updatedAt,
            'Clone timestamp initialization must not affect the original.',
        );
        self::assertSame(
            ['displayName' => 'Copy'],
            $copy->toArray(true, ['castValue', 'defaultValue', 'updatedAt']),
            'Clone serialization must retain NoSnakeCase, exclusions and DoNotCollect metadata.',
        );
    }

    public function testPreserveBulkDataBeforeFirstCloneAccess(): void
    {
        $original = new Dynamic();

        $original->add('count', 'int');

        $original->load(
            ['edit' => ['count' => '7']],
            'edit',
        );

        $copy = clone $original;

        $original->load(
            ['edit' => ['count' => '8']],
            'edit',
        );

        self::assertSame(
            7,
            $copy->getValue('count'),
            'Original bulk writes must preserve the untouched clone value.',
        );
        self::assertSame(
            ['count' => '7'],
            $copy->getData(),
            'Clone must retain its original scoped load data.',
        );
        self::assertSame(
            8,
            $original->getValue('count'),
            'The original must retain its bulk assignment.',
        );
    }

    public function testPreserveDefinitionsBeforeFirstCloneAccess(): void
    {
        $original = new Dynamic();

        $original->add('count', 'int');

        $copy = clone $original;

        $original->add('originalOnly', 'bool');

        self::assertFalse(
            $copy->has('originalOnly'),
            'Original registration must preserve the untouched clone definitions.',
        );
        self::assertTrue(
            $original->has('originalOnly'),
            'The original must retain its new registration.',
        );
    }

    public function testPreserveDynamicDefinitionsAndValuesWithoutDeepCloning(): void
    {
        $original = new Dynamic();
        $value = new stdClass();

        $original->add('count', 'int');
        $original->add('optional', ['string', 'null']);
        $original->add('payload', 'array');
        $original->add('unassigned', 'string');
        $original->setValue('count', '7');
        $original->setValue('optional', null);
        $original->setValue('payload', ['object' => $value]);

        $copy = clone $original;

        self::assertSame(
            $original->getTypes(),
            $copy->getTypes(),
            'Clone must preserve all dynamic definitions.',
        );
        self::assertSame(
            ['count', 'optional', 'payload', 'unassigned'],
            $copy->getNames(),
            'Registration order must survive.',
        );
        self::assertSame(
            ['count' => 7, 'optional' => null, 'payload' => ['object' => $value], 'unassigned' => null],
            $copy->toArray(),
            'Clone must retain dynamic values and user object identity without deep cloning.',
        );

        $copy->setValue('count', '8');
        $copy->setValue('payload', ['copy']);
        $copy->add('copyOnly', 'bool');
        $original->setValue('optional', 'Original only');
        $original->add('originalOnly', 'float');

        self::assertSame(
            7,
            $original->getValue('count'),
            'Dynamic clone writes must not modify original values.',
        );
        self::assertSame(
            8,
            $copy->getValue('count'),
            'Copied dynamic type definitions must still cast assignments.',
        );
        self::assertSame(
            ['object' => $value],
            $original->getValue('payload'),
            'Dynamic arrays must be independently writable.',
        );
        self::assertNull(
            $copy->getValue('optional'),
            'Original dynamic writes must not modify clone values.',
        );
        self::assertFalse(
            $original->has('copyOnly'),
            'Clone registrations must not modify original definitions.',
        );
        self::assertFalse(
            $copy->has('originalOnly'),
            'Original registrations must not modify clone definitions.',
        );
    }

    public function testPreserveLoadedStateWithoutReapplyingDefaultsOrTransformations(): void
    {
        $original = new CloneState();

        $original->load(
            ['edit' => ['cast_value' => 'Value', 'display-name' => ' Name ']],
            'edit',
        );

        $original->displayName = ' Untrimmed ';
        $original->updatedAt = 0;

        $copy = clone $original;

        self::assertSame(
            'Value-cast',
            $copy->castValue,
            'Cloning must not repeat a non-idempotent cast.',
        );
        self::assertSame(
            '',
            $copy->defaultValue,
            'Cloning must not apply a runtime default to existing state.',
        );
        self::assertSame(
            ' Untrimmed ',
            $copy->displayName,
            'Cloning must not trim existing direct values.',
        );
        self::assertSame(
            0,
            $copy->updatedAt,
            'Cloning must not initialize timestamps.',
        );
        self::assertSame(
            'Uncollected',
            $copy->ignored,
            'Uncollected native state must survive cloning.',
        );
        self::assertSame(
            $original->getData(),
            $copy->getData(),
            'Clone must retain the previous load payload.',
        );
        self::assertFalse(
            $copy->isEmpty(),
            'Clone must retain the loaded state.',
        );
        self::assertSame(
            $original->toArray(true),
            $copy->toArray(true),
            'Cloning must preserve serialization state.',
        );
    }

    public function testPreserveStateBeforeFirstCloneAccess(): void
    {
        $original = new CloneHook();

        $original->name = 'Original';

        $original->add('count', 'int');
        $original->setValue('count', 7);

        $copy = clone $original;

        $original->setValue('count', 8);
        $original->add('originalOnly', 'bool');
        $original->setValue('name', 'Changed original');

        self::assertSame(
            7,
            $copy->getValue('count'),
            'Original writes must preserve the untouched clone snapshot.',
        );
        self::assertFalse(
            $copy->has('originalOnly'),
            'Original registrations must not reach an untouched clone.',
        );
        self::assertSame(
            'Original',
            $copy->getValue('name'),
            'The clone collector must read its own native state.',
        );
        self::assertSame(
            8,
            $original->getValue('count'),
            'The original must retain its updated dynamic value.',
        );
    }

    public function testReadDirectPropertyChangesFromEachInitializedModel(): void
    {
        $original = new Country();

        $original->getTypes();

        $copy = clone $original;

        $copy->name = 'Direct copy';
        $original->name = 'Direct original';

        self::assertSame(
            'Direct copy',
            $copy->getValue('name'),
            'Collector must read the clone direct property.',
        );
        self::assertSame(
            'Direct original',
            $original->getValue('name'),
            'Original collector must remain bound to it.',
        );
        self::assertSame(
            ['name' => 'Direct copy'],
            $copy->toArray(),
            'Serialization must observe direct clone writes.',
        );
        self::assertSame(
            ['name' => 'Direct original'],
            $original->toArray(),
            'Original serialization must remain independent.',
        );
    }

    public function testRetainDynamicStateDuringNestedLoad(): void
    {
        $nested = new Dynamic();

        $nested->add('city', 'string');

        $model = new DynamicNested($nested);

        $model->add('count', 'int');
        $model->add('updatedAt', 'timestamp');

        $model->load(
            ['edit' => ['dynamic.city' => 'City', 'count' => '8']],
            'edit',
        );

        self::assertSame(
            'City',
            $model->getValue('dynamic.city'),
            'Nested loading must still traverse the model.'
        );
        self::assertSame(
            8,
            $model->getValue('count'),
            'Nested reads must not detach an active bulk assignment.'
        );
        self::assertGreaterThan(
            0,
            $model->getValue('updatedAt'),
            'Nested reads must not lose dynamic timestamps.'
        );
    }

    #[DataProviderExternal(ModelCloneProvider::class, 'collectorStates')]
    public function testSetValueOnCloneAndOriginalIndependently(Country|CloneHook $original, bool $initialized): void
    {
        $original->name = 'Original';

        if ($initialized) {
            $original->getTypes();
        }

        $copy = clone $original;

        $copy->setValue('name', 'Copy');

        self::assertSame(
            'Original',
            $original->name,
            'Clone assignment must not write to the original.',
        );
        self::assertSame(
            'Copy',
            $copy->name,
            'Clone assignment must update its direct property.',
        );
        self::assertSame(
            'Copy',
            $copy->getValue('name'),
            'Clone reads must agree with its direct property.',
        );

        $original->setValue('name', 'Changed original');

        self::assertSame(
            'Changed original',
            $original->name,
            'Original assignment must update its own property.',
        );
        self::assertSame(
            'Changed original',
            $original->getValue('name'),
            'Original reads must use its own collector.',
        );
        self::assertSame(
            'Copy',
            $copy->getValue('name'),
            'Original assignment must not affect the clone.',
        );
        self::assertSame(
            ['name' => 'Copy'],
            $copy->toArray(),
            'Clone serialization must use clone values.',
        );
    }
}
