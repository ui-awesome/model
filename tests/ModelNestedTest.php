<?php

declare(strict_types=1);

namespace UIAwesome\Model\Tests;

use UIAwesome\Model\Tests\Support\Model\{Address, Country, Profile, User};

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class ModelNestedTest extends \PHPUnit\Framework\TestCase
{
    public function testProperties(): void
    {
        $user = new User(new Profile(new Address(new Country())));

        $this->assertSame(
            [
                'name',
                'profile.bio',
                'profile.publicEmailPersonal',
                'profile.pathAvatar',
                'profile.address.street',
                'profile.address.city',
                'profile.address.country.name',
            ],
            $user->getProperties()
        );
    }

    public function testGetPropertyValue(): void
    {
        $model = new Address(new Country());
        $model->setPropertyValue('country.name', 'Russia');

        $this->assertSame('Russia', $model->getPropertyValue('country.name'));
    }

    public function testGetPropertyValueWithInvalidNestedProperty(): void
    {
        $user = new User(new Profile(new Address(new Country())));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined property: "UIAwesome\Model\Tests\Support\Model\User::address".');

        $user->getPropertyValue('address.nestedAttribute');
    }

    public function testGetPropertyValueUndefinedPropertyException(): void
    {
        $model = new Address(new Country());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined property: "UIAwesome\Model\Tests\Support\Model\Country::noExist');

        $model->getPropertyValue('country.noExist');
    }

    public function testGetRawDataNotNestedException(): void
    {
        $model = new Address(new Country());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined property: "UIAwesome\Model\Tests\Support\Model\Address::profile".');

        $model->getPropertyValue('profile.user');
    }

    public function testLoadPublicField(): void
    {
        $model = new Address(new Country());

        $this->assertEmpty($model->getPropertyValue('country.name'));

        $data = [
            'Address' => [
                'country.name' => 'Russia',
            ],
        ];

        $this->assertTrue($model->load($data));
        $this->assertSame('Russia', $model->getPropertyValue('country.name'));
    }

    public function testSetPropertyValueAndGetPropertyValueSeveralNestedLevels(): void
    {
        $user = new User(new Profile(new Address(new Country())));

        $user->setPropertyValue('name', 'valery');
        $user->setPropertyValue('profile.bio', 'senior software engineer');
        $user->setPropertyValue('profile.address.street', 'ulitsa 9-ya Voronezhskaya');
        $user->setPropertyValue('profile.address.city', 'Voronezh');
        $user->setPropertyValue('profile.address.country.name', 'Russia');

        $this->assertSame('valery', $user->getPropertyValue('name'));
        $this->assertSame('senior software engineer', $user->getPropertyValue('profile.bio'));
        $this->assertSame('ulitsa 9-ya Voronezhskaya', $user->getPropertyValue('profile.address.street'));
        $this->assertSame('Voronezh', $user->getPropertyValue('profile.address.city'));
        $this->assertSame('Russia', $user->getPropertyValue('profile.address.country.name'));
    }

    public function testSetPropertyValuesAndGetPropertyValueSeveralNestedLevels(): void
    {
        $user = new User(new Profile(new Address(new Country())));

        $user->setPropertiesValues(
            [
                'name' => 'valery',
                'profile.bio' => 'senior software engineer',
                'profile.address.street' => 'ulitsa 9-ya Voronezhskaya',
                'profile.address.city' => 'Voronezh',
                'profile.address.country.name' => 'Russia',
            ],
        );

        $this->assertSame('valery', $user->getPropertyValue('name'));
        $this->assertSame('senior software engineer', $user->getPropertyValue('profile.bio'));
        $this->assertSame('ulitsa 9-ya Voronezhskaya', $user->getPropertyValue('profile.address.street'));
        $this->assertSame('Voronezh', $user->getPropertyValue('profile.address.city'));
        $this->assertSame('Russia', $user->getPropertyValue('profile.address.country.name'));
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

        $this->assertTrue($user->load($data));
        $this->assertSame('valery', $user->getPropertyValue('name'));
        $this->assertSame('senior software engineer', $user->getPropertyValue('profile.bio'));
        $this->assertSame('ulitsa 9-ya Voronezhskaya', $user->getPropertyValue('profile.address.street'));
        $this->assertSame('Voronezh', $user->getPropertyValue('profile.address.city'));
        $this->assertSame('Russia', $user->getPropertyValue('profile.address.country.name'));
    }
}
