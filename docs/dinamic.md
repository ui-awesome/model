# Dinamic

Define model class with dynamic properties:

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;

final class Dinamic extends AbstractModel
{
}
```

Create instance of model and set properties:

```php
<?php

use App\Model\Dinamic;

$model = new Dinamic();

// Set properties dinamically
$model->addProperty('name', 'string');
$model->addProperty('age', 'int');
$model->addProperty('is_active', 'bool');
$model->addProperty('created_at', 'timestamp');

$data = [
    'name' => 'John Doe',
    'age' => 30,
    'is_active' => true,
];

// set values
$model->load($data, 'Dinamic');


// get values
echo $model->getPropertyValue('name'); // John Doe
echo $model->getPropertyValue('age'); // 30
echo $model->getPropertyValue('is_active'); // 1
echo $model->getPropertyValue('created_at'); // timestamp
```
