# UIAwesome model

The UIAwesome Model package provides a robust set of tools for managing data models in PHP applications.

It simplifies the process of creating, retrieving, updating, and deleting model entities, making it an essential tool
for developers working with PHP.

## Usage

To use this library, you need to create a class that extends the AbstractdModel class and define the property you want
to use. For example:

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;

final class User extends AbstractModel
{
    private string $name = '';
    public int $age = 0;
    public bool $isActive = false;
}
```

Operations on the model are performed using the methods provided by the `AbstractModel::class`.

- [Load data from array](#load-data-from-array)
- [Get property value](#get-property-value)
- [Get raw data](#get-raw-data)
- [Retrieve form name](#retrieve-form-name)
- [Set properties values](#set-properties-values)
- [Set property value](#set-property-value)
- [Retrieve all properties](#retrieve-all-properties)
- [Retrieve all properties for type](#retrieve-all-properties-for-type)
- [Check if property exists](#check-if-property-exists)
- [Check type of property](#check-type-of-property)
- [Check if model is empty](#check-if-model-is-empty)
- [Add property dinamically](#add-property-dinamically)
- [Special attributes](#special-attributes)
- [Convert model to array](#convert-model-to-array)

### Load data from array

The `load` method is used to load data into the model.

The method accepts two parameters:

- data: The data to be loaded into the model.
- formName: The name of the model that the data should be loaded into. For default its `null`.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

$data = [
    'User' => [
        'name' => 'John Doe',
        'age' => 30,
        'isActive' => true,
    ],
];

// load data
$model->load($data);
```

With form name.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

$data = [
    'name' => 'John Doe',
    'age' => 30,
    'isActive' => true,
];

// load data
$model->load($data, 'User');
```

With request object.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

// load data
$model->load($request->getParsedBody());
```

### Get property value

The `getPropertyValue()` method is used to retrieve the value of a property.

The method accepts one parameter:

- property: The name of the property to retrieve.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

// load data
$model->load($request->getParsedBody());

// get values
echo $model->getPropertyValue('name');
echo $model->getPropertyValue('age');
```

### Get raw data

The `getData()` method is used to retrieve the raw data for the model.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

// load data
$model->load($request->getParsedBody());

// get raw data
$data = $model->getData();
```

### Retrieve form name

The `getFormName()` method is used to retrieve the form name that this model class should use.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

echo $model->getFormName();
```

### Set properties values

The `setPropertiesValues()` method is used to set the values for multiple properties.

The method accepts one parameter:

- data: The data to be set into the model.
- exceptProperties: Exclude properties from the array. Default is `[]`.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

// set values
$model->setPropertiesValues(
    [
        'name' => 'John Doe',
        'age' => 30,
        'isActive' => true,
    ]
);
```

### Set property value

The `setPropertyValue()` method is used to set the value for the specified property.

The method accepts two parameters:

- property: The name of the property to set.
- value: The value to set.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

// set values
$model->setPropertyValue('name', 'John Doe');
$model->setPropertyValue('age', 30);
$model->setPropertyValue('isActive', true);
```

### Retrieve all properties

The `getProperties()` method is used to retrieve the list of properties names.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

$properties = $model->getProperties();
```

### Retrieve all properties for type

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

$propertiesType = $model->getPropertiesType();
```

### Check if property exists

The `hasProperty()` method is used to check if a property exists.

The method accepts one parameter:

- property: The name of the property to check.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

$model->hasProperty('name');
```

### Check type of property

The `isPropertyType()` method is used to check the type of property.

The method accepts two parameters:

- property: The name of the property to check.
- type: The type to check.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

$model->isPropertyType('name', 'string');
```

### Check if model is empty

The `isEmpty()` method is used to check if the model is empty.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

$model->isEmpty();
```

### Add property dinamically

The `addProperty()` method is used to add property to model.

The method accepts two parameters:

- property: The name of the property to add.
- type: The type of the property to add.

```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

$model->addProperty('name', 'string');
$model->addProperty('age', 'int');
$model->addProperty('isActive', 'bool');
```

### Special attributes

- `#[DoNotCollect]` - Do not collect this property.
- `#[Timestamp]` - Set timestamp when property is updated.

### Convert model to array

The `toArray()` method is used to convert the model to an array.

The method accepts two parameters:

- snakeCase: Convert the properties to snake case. Default is `false`.
- exceptProperties: Exclude properties from the array. Default is `[]`.


```php
<?php

declare(strict_types=1);

use App\Model\User;

$model = new User();

$model->addProperty('name', 'string');
$model->addProperty('age', 'int');

$model->setPropertiesValues(
    [
        'name' => 'John Doe',
        'age' => 30,
    ]
);

$data = $model->toArray();
```

### Examples

- [basic](/docs/basic.md)
- [nested](/docs/nested.md)
- [dinamic](/docs/dinamic.md)
- [dinamic-nested](/docs/dinamic-nested.md)

### Methods

Refer to the [Tests](https://github.com/php-forge/model/blob/main/tests) for comprehensive examples.

The following methods are available for setting and retrieving model data.

| Method                  | Description                                                                                |
| ----------------------- | ------------------------------------------------------------------------------------------ |
| `addProperty()`         | Add property to model.                                                                     |
| `getData()`             | Returns the raw data for the model.                                                        |
| `getModelName()`        | Returns the name of the model.                                                             |
| `getProperties()`       | Return The list of properties names.                                                       |
| `getPropertyTypes()`    | Returns the list of property types indexed by property names.                              |
| `getPropertyValue()`    | Returns the value (raw data) for the specified property.                                   |
| `hasProperty()`         | Check if property exists.                                                                  |
| `isEmpty()`             | Check if model is empty.                                                                   |
| `isPropertyType()`      | Check type of property.                                                                    |
| `load()`                | Load data into the model.                                                                  |
| `setPropertiesValues()` | Set the values for multiple properties.                                                    |
| `setPropertyValue()`    | Set the value for the specified property.                                                  |
| `toArray()`             | Convert the model to an array.                                                             |
