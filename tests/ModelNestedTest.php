<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\{DataProviderExternal, Group};
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\BaseModel;
use UIAwesome\Model\Exception\Message;
use UIAwesome\Model\Tests\Provider\ModelNestedProvider;
use UIAwesome\Model\Tests\Support\Model\{Address, Country, Dynamic, NestedPaths, Profile, User};

/**
 * Unit tests for nested property access and assignment across composed model graphs.
 *
 * {@see ModelNestedProvider} for test case data providers.
 */
#[Group('nested')]
final class ModelNestedTest extends TestCase
{
    public function testFlattenPresentNullableModelNamesWithoutFlatteningAmbiguousUnion(): void
    {
        self::assertSame(
            [
                'address.city',
                'address.street',
                'address.country.name',
                'ambiguous',
                'name',
                'required.city',
                'required.street',
                'required.country.name',
            ],
            (new NestedPaths())->getNames(),
            'Nullable and required models must flatten equally; ambiguous unions must stay flat.',
        );
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'presentModelPaths')]
    public function testHasPresentNullableAndRequiredModelPaths(string $property): void
    {
        self::assertTrue(
            (new NestedPaths())->has($property),
            'Present unambiguous model paths must exist.',
        );
    }

    public function testKeepAbsentNullableModelFlatWithoutCreatingAnInstance(): void
    {
        $model = new NestedPaths();

        $model->address = null;

        self::assertTrue(
            $model->has('address'),
            'The nullable root property must still exist.',
        );
        self::assertFalse(
            $model->has('address.city'),
            'Absent models must not expose nested paths.',
        );
        self::assertSame(
            ['address', 'ambiguous', 'name', 'required.city', 'required.street', 'required.country.name'],
            $model->getNames(),
            'An absent nullable model must remain a flat name.',
        );
        self::assertNull(
            $model->getValue('address'),
            'Queries must not instantiate an absent model.',
        );
    }

    public function testKeepAmbiguousModelUnionGuardedWhilePreservingExistingInstanceReads(): void
    {
        $model = new NestedPaths();

        self::assertFalse(
            $model->has('ambiguous.city'),
            'Ambiguous unions must not become assignable nested paths.',
        );
        self::assertSame(
            '',
            $model->getValue('ambiguous.city'),
            'Existing instance-driven reads must remain valid.',
        );
        self::assertSame(
            'Original',
            $model->getValue('name'),
            'Direct scalar reads must remain valid.',
        );
    }

    public function testKeepCollectingNamesAfterNestedModelProperty(): void
    {
        $model = new class extends BaseModel {
            public Address $address;
            public string $label = '';

            public function __construct()
            {
                $this->address = new Address(new Country());
            }
        };

        self::assertSame(
            ['address.city', 'address.street', 'address.country.name', 'label'],
            $model->getNames(),
            'Should continue collecting property names declared after a nested model property.',
        );
    }

    public function testKeepNonStringKeyedModelPropertyFlatWhenCollectingNames(): void
    {
        $model = new Dynamic();

        $model->add('0', Country::class);
        $model->setValue('0', new Country());

        self::assertSame(
            ['0'],
            $model->getNames(),
            'Non-string property key must not be flattened, even when typed as a model.',
        );
    }

    public function testLoadNestedPropertyFromScopedPayload(): void
    {
        $model = new Address(new Country());

        self::assertEmpty(
            $model->getValue('country.name'),
            'Should start with an empty nested property value.',
        );

        $data = ['Address' => ['country.name' => 'Russia']];

        self::assertTrue(
            $model->load($data),
            'Should load nested properties from scoped payload data.',
        );
        self::assertSame(
            'Russia',
            $model->getValue('country.name'),
            'Should set the loaded nested property value.',
        );
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'presentModelPaths')]
    public function testLoadPresentNullableAndRequiredModelPathsWithExplicitScope(string $property): void
    {
        $model = new NestedPaths();

        self::assertTrue(
            $model->load(['edit' => [$property => 'Santiago']], 'edit'),
            'Scoped data must load.',
        );
        self::assertSame(
            'Santiago',
            $model->getValue($property),
            'Scoped load must update the requested leaf.',
        );
        self::assertSame(
            [$property => 'Santiago'],
            $model->getData(),
            'Loaded data must exclude its scope wrapper.',
        );
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'nestedPropertyValues')]
    public function testLoadSeveralNestedProperties(string $property, string $expectedValue): void
    {
        $user = new User(new Profile(new Address(new Country())));

        $data = [
            'User' => [
                'name' => 'valery',
                'profile.bio' => 'senior software engineer',
                'profile.address.street' => 'ulitsa 9-ya Voronezhskaya',
                'profile.address.city' => 'Voronezh',
                'profile.address.country.name' => 'Russia',
            ],
        ];

        self::assertTrue(
            $user->load($data),
            'Should load all provided nested paths in a single operation.',
        );
        self::assertSame(
            $expectedValue,
            $user->getValue($property),
            'Should return the expected value for each loaded nested property path.',
        );
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'presentModelPaths')]
    public function testReadPresentNullableAndRequiredModelPaths(string $property): void
    {
        self::assertSame(
            '',
            (new NestedPaths())->getValue($property),
            'Valid nested reads must retain their values.',
        );
    }

    /**
     * @phpstan-param list<string> $types
     */
    #[DataProviderExternal(ModelNestedProvider::class, 'nullableModelTypes')]
    public function testRecognizeNullableDynamicModelRegardlessOfTypeOrder(array $types): void
    {
        $model = new Dynamic();

        $model->add('address', $types);
        $model->setValue('address', new Address(new Country()));

        self::assertTrue(
            $model->has('address.city'),
            'Nullable metadata order must not change path lookup.',
        );
        self::assertSame(
            ['address.city', 'address.street', 'address.country.name'],
            $model->getNames(),
            'Nullable metadata order must not change flattened names.',
        );
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'invalidModelPaths')]
    public function testRejectInvalidSegmentsInHas(string $property, string $class, string $undefined): void
    {
        self::assertFalse(
            (new NestedPaths())->has($property),
            'Invalid path segments must not exist.',
        );
    }

    public function testReturnFlattenedPropertyPathsForNestedModels(): void
    {
        $user = new User(new Profile(new Address(new Country())));

        self::assertSame(
            [
                'name',
                'profile.bio',
                'profile.pathAvatar',
                'profile.publicEmailPersonal',
                'profile.address.city',
                'profile.address.street',
                'profile.address.country.name',
            ],
            $user->getNames(),
            'Should return all nested properties as flattened dot-notated paths.',
        );
    }

    public function testReturnNestedPropertyValueAfterAssignment(): void
    {
        $model = new Address(new Country());
        $model->setValue('country.name', 'Russia');

        self::assertSame(
            'Russia',
            $model->getValue('country.name'),
            'Should return the assigned value for a nested property path.',
        );
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'nestedPropertyValues')]
    public function testSetNestedPropertyValueAcrossSeveralLevels(string $property, string $expectedValue): void
    {
        $user = new User(new Profile(new Address(new Country())));

        $user->setValue('name', 'valery');
        $user->setValue('profile.bio', 'senior software engineer');
        $user->setValue('profile.address.street', 'ulitsa 9-ya Voronezhskaya');
        $user->setValue('profile.address.city', 'Voronezh');
        $user->setValue('profile.address.country.name', 'Russia');

        self::assertSame(
            $expectedValue,
            $user->getValue($property),
            'Should return the expected value after assigning each nested property path individually.',
        );
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'nestedPropertyValues')]
    public function testSetNestedPropertyValuesWithBulkAssignment(string $property, string $expectedValue): void
    {
        $user = new User(new Profile(new Address(new Country())));

        $user->setValues(
            [
                'name' => 'valery',
                'profile.bio' => 'senior software engineer',
                'profile.address.street' => 'ulitsa 9-ya Voronezhskaya',
                'profile.address.city' => 'Voronezh',
                'profile.address.country.name' => 'Russia',
            ],
        );

        self::assertSame(
            $expectedValue,
            $user->getValue($property),
            'Should return the expected value after assigning nested paths with setValues().',
        );
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'guardedModelPaths')]
    public function testThrowInvalidArgumentExceptionWhenLoadingGuardedModelPath(bool $absent): void
    {
        $model = new NestedPaths();
        $model->address = null;
        $property = $absent ? 'address.city' : 'ambiguous.city';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::UNDEFINED_PROPERTY->getMessage($property),
        );

        $model->load(['edit' => [$property => 'Rejected']], 'edit');
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'invalidModelPaths')]
    public function testThrowInvalidArgumentExceptionWhenLoadingInvalidSegments(
        string $property,
        string $class,
        string $undefined,
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::UNDEFINED_PROPERTY->getMessage($property),
        );

        (new NestedPaths())->load(['edit' => [$property => 'Rejected']], 'edit');
    }

    public function testThrowInvalidArgumentExceptionWhenNestedPathStartsWithUnknownProperty(): void
    {
        $user = new User(new Profile(new Address(new Country())));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::UNDEFINED_PROPERTY_WITH_CLASS->getMessage(
                User::class,
                'address',
            ),
        );

        $user->getValue('address.nestedAttribute');
    }

    public function testThrowInvalidArgumentExceptionWhenReadingAbsentNullableModelPath(): void
    {
        $model = new NestedPaths();
        $model->address = null;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::UNDEFINED_PROPERTY_WITH_CLASS->getMessage(NestedPaths::class, 'address.city'),
        );

        $model->getValue('address.city');
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'invalidModelPaths')]
    public function testThrowInvalidArgumentExceptionWhenReadingInvalidSegments(
        string $property,
        string $class,
        string $undefined,
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::UNDEFINED_PROPERTY_WITH_CLASS->getMessage($class, $undefined),
        );

        (new NestedPaths())->getValue($property);
    }

    public function testThrowInvalidArgumentExceptionWhenReadingMissingNestedBranch(): void
    {
        $model = new Address(new Country());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::UNDEFINED_PROPERTY_WITH_CLASS->getMessage(
                Address::class,
                'profile',
            ),
        );

        $model->getValue('profile.user');
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'nonModelBranchValues')]
    public function testThrowInvalidArgumentExceptionWhenReadingThroughNonModelValue(mixed $value): void
    {
        $model = new Dynamic();
        $model->add('branch', 'mixed');
        $model->setValue('branch', $value);

        self::assertSame(
            $value,
            $model->getValue('branch'),
            'A direct non-model read must preserve its value.',
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::UNDEFINED_PROPERTY_WITH_CLASS->getMessage(Dynamic::class, 'branch.leaf'),
        );

        $model->getValue('branch.leaf');
    }

    public function testThrowInvalidArgumentExceptionWhenReadingUndefinedNestedProperty(): void
    {
        $model = new Address(new Country());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::UNDEFINED_PROPERTY_WITH_CLASS->getMessage(
                Country::class,
                'noExist',
            ),
        );

        $model->getValue('country.noExist');
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'guardedModelPaths')]
    public function testThrowInvalidArgumentExceptionWhenWritingGuardedModelPath(bool $absent): void
    {
        $model = new NestedPaths();
        $model->address = null;
        $property = $absent ? 'address.city' : 'ambiguous.city';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::UNDEFINED_PROPERTY->getMessage($property),
        );

        $model->setValue($property, 'Rejected');
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'invalidModelPaths')]
    public function testThrowInvalidArgumentExceptionWhenWritingInvalidSegments(
        string $property,
        string $class,
        string $undefined,
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            Message::UNDEFINED_PROPERTY->getMessage($property),
        );

        (new NestedPaths())->setValue($property, 'Rejected');
    }

    #[DataProviderExternal(ModelNestedProvider::class, 'presentModelPaths')]
    public function testWritePresentNullableAndRequiredModelPaths(string $property): void
    {
        $model = new NestedPaths();
        $model->setValue($property, 'Santiago');

        self::assertSame(
            'Santiago',
            $model->getValue($property),
            'Nested assignment must update the requested leaf.'
        );
    }
}
