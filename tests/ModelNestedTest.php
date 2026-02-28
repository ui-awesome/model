<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use UIAwesome\Model\Tests\Support\Model\{Address, Country, Profile, User};

final class ModelNestedTest extends TestCase
{
    public function testGetPropertyValue(): void
    {
        $model = new Address(new Country());
        $model->setPropertyValue('country.name', 'Russia');

        self::assertSame('Russia', $model->getPropertyValue('country.name'));
    }

    public function testGetPropertyValueUndefinedPropertyException(): void
    {
        $model = new Address(new Country());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined property: "UIAwesome\Model\Tests\Support\Model\Country::noExist');

        $model->getPropertyValue('country.noExist');
    }

    public function testGetPropertyValueWithInvalidNestedProperty(): void
    {
        $user = new User(new Profile(new Address(new Country())));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined property: "UIAwesome\Model\Tests\Support\Model\User::address".');

        $user->getPropertyValue('address.nestedAttribute');
    }

    public function testGetRawDataNotNestedException(): void
    {
        $model = new Address(new Country());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined property: "UIAwesome\Model\Tests\Support\Model\Address::profile".');

        $model->getPropertyValue('profile.user');
    }

    public function testLoadPublicField(): void
    {
        $model = new Address(new Country());

        self::assertEmpty($model->getPropertyValue('country.name'));

        $data = [
            'Address' => [
                'country.name' => 'Russia',
            ],
        ];

        self::assertTrue($model->load($data));
        self::assertSame('Russia', $model->getPropertyValue('country.name'));
    }

    public function testLoadSeveralNested(): void
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

        self::assertTrue($user->load($data));
        self::assertSame('valery', $user->getPropertyValue('name'));
        self::assertSame('senior software engineer', $user->getPropertyValue('profile.bio'));
        self::assertSame('ulitsa 9-ya Voronezhskaya', $user->getPropertyValue('profile.address.street'));
        self::assertSame('Voronezh', $user->getPropertyValue('profile.address.city'));
        self::assertSame('Russia', $user->getPropertyValue('profile.address.country.name'));
    }

    public function testProperties(): void
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
            $user->getProperties(),
        );
    }

    public function testSetPropertyValueAndGetPropertyValueSeveralNestedLevels(): void
    {
        $user = new User(new Profile(new Address(new Country())));

        $user->setPropertyValue('name', 'valery');
        $user->setPropertyValue('profile.bio', 'senior software engineer');
        $user->setPropertyValue('profile.address.street', 'ulitsa 9-ya Voronezhskaya');
        $user->setPropertyValue('profile.address.city', 'Voronezh');
        $user->setPropertyValue('profile.address.country.name', 'Russia');

        self::assertSame('valery', $user->getPropertyValue('name'));
        self::assertSame('senior software engineer', $user->getPropertyValue('profile.bio'));
        self::assertSame('ulitsa 9-ya Voronezhskaya', $user->getPropertyValue('profile.address.street'));
        self::assertSame('Voronezh', $user->getPropertyValue('profile.address.city'));
        self::assertSame('Russia', $user->getPropertyValue('profile.address.country.name'));
    }

    public function testSetPropertyValuesAndGetPropertyValueSeveralNestedLevels(): void
    {
        $user = new User(new Profile(new Address(new Country())));

        $user->setProperties(
            [
                'name' => 'valery',
                'profile.bio' => 'senior software engineer',
                'profile.address.street' => 'ulitsa 9-ya Voronezhskaya',
                'profile.address.city' => 'Voronezh',
                'profile.address.country.name' => 'Russia',
            ],
        );

        self::assertSame('valery', $user->getPropertyValue('name'));
        self::assertSame('senior software engineer', $user->getPropertyValue('profile.bio'));
        self::assertSame('ulitsa 9-ya Voronezhskaya', $user->getPropertyValue('profile.address.street'));
        self::assertSame('Voronezh', $user->getPropertyValue('profile.address.city'));
        self::assertSame('Russia', $user->getPropertyValue('profile.address.country.name'));
    }
}
