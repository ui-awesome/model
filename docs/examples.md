# Usage examples

This document provides practical examples for declared models, nested models, and dynamic properties.

## Basic model

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\Attribute\{DefaultValue, MapFrom, NoSnakeCase, Trim};
use UIAwesome\Model\BaseModel;

final class User extends BaseModel
{
    #[NoSnakeCase]
    public string $apiVersion = 'v1';

    #[DefaultValue('Guest')]
    public string $displayName = '';

    #[Trim]
    public string $name = '';

    #[MapFrom('user-age')]
    public int $age = 0;
}

$model = new User();

$model->load(['User' => ['name' => 'Jane', 'user-age' => 20]]);
echo $model->getValue('name');
// "Jane"
echo $model->getValue('age');
// 20
```

## Preserve selected key casing in serialized output

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\Attribute\NoSnakeCase;
use UIAwesome\Model\BaseModel;

final class ApiPayload extends BaseModel
{
    #[NoSnakeCase]
    public string $apiVersion = 'v2';

    public string $publicEmailPersonal = 'admin@example.com';
}

$model = new ApiPayload();

$data = $model->toArray(snakeCase: true);
// ['apiVersion' => 'v2', 'public_email_personal' => 'admin@example.com']
```

## Explicit payload-key mapping

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\Attribute\MapFrom;
use UIAwesome\Model\BaseModel;

final class JsonLdContext extends BaseModel
{
    #[MapFrom('@context')]
    public string $context = '';
}

$model = new JsonLdContext();

$model->setValues(['@context' => 'https://schema.org']);
echo $model->getValue('context');
// "https://schema.org"
```

## Nested model access

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\BaseModel;

final class Address extends BaseModel
{
    public string $city = '';
    public string $street = '';
}

final class Profile extends BaseModel
{
    public function __construct(public readonly Address $address) {}
}

$profile = new Profile(new Address());

$profile->setValue('address.city', 'Madrid');
echo $profile->getValue('address.city');
// "Madrid"
```

## Dynamic properties

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\BaseModel;

final class DynamicModel extends BaseModel {}

$model = new DynamicModel();

$model->add('name', 'string');
$model->add('age', 'int');
$model->add('createdAt', 'timestamp');

$model->load(
    [
        'name' => 'John Doe',
        'age' => 30,
    ],
    'DynamicModel',
);
echo $model->getValue('name');
// "John Doe"
echo $model->getValue('createdAt');
// current timestamp
```

## Serialization

```php
$array = $model->toArray(snakeCase: true, exceptProperties: ['createdAt']);
```

## DateTime casting

```php
<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;
use UIAwesome\Model\BaseModel;

final class Post extends BaseModel
{
    public DateTimeImmutable $publishedAt;
}

$model = new Post();

$model->setValue('publishedAt', '2026-03-01T10:00:00+00:00');
echo $model->getValue('publishedAt')->format(DATE_ATOM);
// "2026-03-01T10:00:00+00:00"
```

## Trim normalization

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\Attribute\Trim;
use UIAwesome\Model\BaseModel;

final class Profile extends BaseModel
{
    #[Trim]
    public string $displayName = '';
}

$model = new Profile();

$model->setValue('displayName', '  Ada  ');
echo $model->getValue('displayName');
// "Ada"
```

## Forced casting with `Cast`

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\Attribute\Cast;
use UIAwesome\Model\BaseModel;

final class SearchFilter extends BaseModel
{
    #[Cast('array')]
    public array $tags = [];
}

$model = new SearchFilter();

$model->setValue('tags', 'php, yii2, model');
print_r($model->getValue('tags'));
// Array ( [0] => php [1] => yii2 [2] => model )
```

## Runtime defaults with `DefaultValue`

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\Attribute\DefaultValue;
use UIAwesome\Model\BaseModel;

final class Profile extends BaseModel
{
    #[DefaultValue('Guest')]
    public string $displayName = '';
}

$model = new Profile();

$model->setValue('displayName', '');
echo $model->getValue('displayName');
// "Guest"
```

## Next steps

- 📚 [Installation guide](installation.md)
- ⚙️ [Configuration reference](configuration.md)
- 🧪 [Testing guide](testing.md)
