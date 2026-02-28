# Usage examples

This document provides practical examples for declared models, nested models, and dynamic properties.

## Basic model

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;

final class User extends AbstractModel
{
    public string $name = '';
    public int $age = 0;
}

$model = new User();
$model->load(['User' => ['name' => 'Jane', 'age' => 20]]);

echo $model->getPropertyValue('name');
echo $model->getPropertyValue('age');
```

## Nested model access

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;

final class Address extends AbstractModel
{
    public string $city = '';
    public string $street = '';
}

final class Profile extends AbstractModel
{
    public function __construct(public readonly Address $address) {}
}

$profile = new Profile(new Address());
$profile->setPropertyValue('address.city', 'Madrid');

echo $profile->getPropertyValue('address.city');
```

## Dynamic properties

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;

final class DynamicModel extends AbstractModel {}

$model = new DynamicModel();

$model->addProperty('name', 'string');
$model->addProperty('age', 'int');
$model->addProperty('createdAt', 'timestamp');

$model->load([
    'name' => 'John Doe',
    'age' => 30,
], 'DynamicModel');

echo $model->getPropertyValue('name');
echo $model->getPropertyValue('createdAt');
```

## Serialization

```php
$array = $model->toArray(snakeCase: true, exceptProperties: ['createdAt']);
```

## Next steps

- 📚 [Installation guide](installation.md)
- ⚙️ [Configuration reference](configuration.md)
- 🧪 [Testing guide](testing.md)
