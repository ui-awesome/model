<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\BaseModel;
use UIAwesome\Model\Exception\Message;
use UIAwesome\Model\Tests\Provider\ModelNestedProvider;
use UIAwesome\Model\Tests\Support\Model\{Address, Country, Profile, User};

/**
 * Unit tests for nested property access and assignment across composed model graphs.
 *
 * Test coverage.
 * - Loads and assigns deeply nested paths, then reads the expected values for each nested segment.
 * - Returns flattened nested property lists for composed models.
 * - Throws invalid argument exceptions for undefined nested paths and non-nested base properties.
 *
 * {@see ModelNestedProvider} for test case data providers.
 *
 * @copyright Copyright (C) 2024 Terabytesoftw.
 * @license https://opensource.org/license/bsd-3-clause BSD 3-Clause License.
 */
final class ModelNestedTest extends TestCase
{
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
}
