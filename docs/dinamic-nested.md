## Dinamic nested

Define model class with dynamic properties:

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;

final class DinamicAddress extends AbstractModel
{
}
```

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;

final class DinamicNested extends AbstractModel
{
    // define nested model
    public function __construct(private DinamicAddress $address)
    {
    }    
}
```

Create instance of model and set properties:

```php
<?php

use App\Model\DinamicNested;

$modelNested = new DinamicAddress();

// Set properties dinamically
$modelNested->addProperty('street', 'string');
$modelNested->addProperty('city', 'string');
$modelNested->addProperty('zip', 'string');
$modelNested->addProperty('createdAt', 'timestamp');

// Create instance of nested model
$model = new DinamicNested($modelNested);

$model->addProperty('name', 'string');
$model->addProperty('age', 'int');
$model->addProperty('email', 'string');

// set values
$data = [
    'name' => 'John Doe',
    'age' => 30,
    'email' => 'test@example.com',
    'address.city' => 'New York',
    'address.state' => 'NY',
    'address.zip' => '10001',
];

$model->load($data, 'DinamicNested');

// get values
echo $model->getPropertyValue('name'); // John Doe
echo $model->getPropertyValue('age'); // 30
echo $model->getPropertyValue('email'); //
echo $model->getPropertyValue('address.city'); // New York
echo $model->getPropertyValue('address.state'); // NY
echo $model->getPropertyValue('address.zip'); // 10001
```
