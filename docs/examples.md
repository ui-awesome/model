# Usage examples

This document provides practical examples for declared models, nested models, and dynamic properties.

## Basic model

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\{MapFrom, NoSnakeCase, Trim};

final class User extends AbstractModel
{
    #[NoSnakeCase]
    public string $apiVersion = 'v1';

    #[Trim]
    public string $name = '';

    #[MapFrom('user-age')]
    public int $age = 0;
}

$model = new User();

$model->load(['User' => ['name' => 'Jane', 'user-age' => 20]]);
echo $model->getPropertyValue('name');
// "Jane"
echo $model->getPropertyValue('age');
// 20
```

## Preserve selected key casing in serialized output

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\NoSnakeCase;

final class ApiPayload extends AbstractModel
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

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\MapFrom;

final class JsonLdContext extends AbstractModel
{
    #[MapFrom('@context')]
    public string $context = '';
}

$model = new JsonLdContext();

$model->setProperties(['@context' => 'https://schema.org']);
echo $model->getPropertyValue('context');
// "https://schema.org"
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
// "Madrid"
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

$model->load(
    [
        'name' => 'John Doe',
        'age' => 30,
    ],
    'DynamicModel',
);
echo $model->getPropertyValue('name');
// "John Doe"
echo $model->getPropertyValue('createdAt');
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
use UIAwesome\Model\AbstractModel;

final class Post extends AbstractModel
{
    public DateTimeImmutable $publishedAt;
}

$model = new Post();

$model->setPropertyValue('publishedAt', '2026-03-01T10:00:00+00:00');
echo $model->getPropertyValue('publishedAt')->format(DATE_ATOM);
// "2026-03-01T10:00:00+00:00"
```

## Trim normalization

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\Trim;

final class Profile extends AbstractModel
{
    #[Trim]
    public string $displayName = '';
}

$model = new Profile();

$model->setPropertyValue('displayName', '  Ada  ');
echo $model->getPropertyValue('displayName');
// "Ada"
```

## Forced casting with `Cast`

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\Cast;

final class SearchFilter extends AbstractModel
{
    #[Cast('array')]
    public array $tags = [];
}

$model = new SearchFilter();

$model->setPropertyValue('tags', 'php, yii2, model');
print_r($model->getPropertyValue('tags'));
// Array ( [0] => php [1] => yii2 [2] => model )
```

## Next steps

- 📚 [Installation guide](installation.md)
- ⚙️ [Configuration reference](configuration.md)
- 🧪 [Testing guide](testing.md)
