# Basic

Define model class with properties:

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\{AbstractModel, Attribute\DoNotCollect, Attribute\Timestamp};

final class User extends AbstractModel
{
    private int $age;
    private string $name;
    #[DoNotCollect] // attribute for do not collect
    private int $flag = 0;
    private bool $isActive;
    #[Timestamp] // attribute for timestamp
    private int $updatedAt = 0;
}
```

Create instance of model and set properties:

```php
<?php

use App\Model\User;

$model = new User();

// set values
$model->load($request->getParsedBody());

// get values
echo $model->getPropertyValue('name');
echo $model->getPropertyValue('age');
echo $model->getPropertyValue('isActive');
echo $model->getPropertyValue('updatedAt');
```
