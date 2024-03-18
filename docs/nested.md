# Nested

Define model class with nested properties:

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;

final class Address extends AbstractModel
{
    private string $street;
    private string $city;
    private string $zip;
    private int $createdAt;
}
```

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;

final class User extends AbstractModel
{
    private int $age;
    private string $name;
    
    public function __construct(private readonly Address $address)
    {
    }
}
```

Create instance of model and set properties:

```php
<?php

use App\Model\User;

$address = new Address();
$model = new User($address);

// set values
$model->load(
    [
        'name' => 'John Doe',
        'age' => 30,
        'address' => [
            'street' => 'Main street',
            'city' => 'New York',
            'zip' => '10001',
        ],
    ]
);

// get values
echo $model->getPropertyValue('name');
echo $model->getPropertyValue('age');
echo $model->getPropertyValue('address.street');
echo $model->getPropertyValue('address.city');
echo $model->getPropertyValue('address.zip');
echo $model->getPropertyValue('address.createdAt');
```
