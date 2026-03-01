<!-- markdownlint-disable MD041 -->
<p align="center">
    <a href="https://github.com/ui-awesome/model" target="_blank">
        <img src="https://avatars.githubusercontent.com/u/103309199?s%25253D400%252526u%25253Dca3561c692f53ed7eb290d3bb226a2828741606f%252526v%25253D4" height="100px" alt="UIAwesome">
    </a>
    <h1 align="center">UIAwesome Model for PHP</h1>
    <br>
</p>
<!-- markdownlint-enable MD041 -->

<p align="center">
    <a href="https://github.com/ui-awesome/model/actions/workflows/build.yml" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/ui-awesome/model/build.yml?style=for-the-badge&label=PHPUnit&logo=github" alt="PHPUnit">
    </a>
    <a href="https://dashboard.stryker-mutator.io/reports/github.com/ui-awesome/model/main" target="_blank">
        <img src="https://img.shields.io/endpoint?style=for-the-badge&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fui-awesome%2Fmodel%2Fmain" alt="Mutation Testing">
    </a>
    <a href="https://github.com/ui-awesome/model/actions/workflows/static.yml" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/ui-awesome/model/static.yml?style=for-the-badge&label=PHPStan&logo=github" alt="PHPStan">
    </a>
</p>

<p align="center">
    <strong>Typed model mapping for modern PHP applications</strong><br>
    <em>Nested properties, explicit input mapping, trim normalization, custom casting, and selective key serialization</em>
</p>

## Features

<picture>
    <source media="(min-width: 768px)" srcset="./docs/svgs/features.svg">
    <img src="./docs/svgs/features-mobile.svg" alt="Feature Overview" style="width: 100%;">
</picture>

## Installation

```bash
composer require ui-awesome/model:^0.2
```

## Quick start

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\{Cast, MapFrom, NoSnakeCase, Timestamp, Trim};

final class User extends AbstractModel
{
    #[NoSnakeCase]
    public string $apiVersion = 'v1';

    #[MapFrom('user-email-address')]
    public string $email = '';

    #[Trim]
    public string $name = '';

    #[Cast('array')]
    public array $tags = [];

    #[Timestamp]
    private int $updatedAt = 0;
}

$model = new User();

$model->load(
    [
        'User' => [
            'apiVersion' => 'v2',
            'name' => '  Ada Lovelace  ',
            'tags' => 'php, yii2, model',
            'user-email-address' => 'ada@example.com',
        ],
    ],
);

$types = $model->getPropertyTypes();
/*
[
    'apiVersion' => 'string',
    'name' => 'string',
    'email' => 'string',
    'tags' => 'array',
    'updatedAt' => 'timestamp'
]
*/
$payload = $model->toArray(snakeCase: true, exceptProperties: ['updatedAt']);
/*
[
    'apiVersion' => 'v2',
    'name' => 'Ada Lovelace',
    'email' => 'ada@example.com',
    'tags' => ['php', 'yii2', 'model']
]
*/
```

## Explicit payload mapping with `MapFrom`

Use `#[MapFrom('external-key')]` when incoming payload keys do not follow snake_case or camelCase naming.

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\MapFrom;

final class JsonLdPayload extends AbstractModel
{
    #[MapFrom('@context')]
    public string $context = '';
}

$payload = new JsonLdPayload();

$payload->setProperties(['@context' => 'https://schema.org']);
```

## Automatic input trimming with `Trim`

Use `#[Trim]` to normalize leading and trailing spaces for string values during assignment.

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

$profile = new Profile();

$profile->setProperties(['display_name' => '  Ada Lovelace  ']);
```

## Forced custom casting with `Cast`

Use `#[Cast('array')]` to transform transport formats such as comma-separated strings.

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

$filter = new SearchFilter();

$filter->setPropertyValue('tags', 'php, yii2, model');
```

## Preserve selected output keys with `NoSnakeCase`

Use `#[NoSnakeCase]` to keep specific property names unchanged when serializing with `snakeCase: true`.

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\AbstractModel;
use UIAwesome\Model\Attribute\NoSnakeCase;

final class ApiPayload extends AbstractModel
{
    #[NoSnakeCase]
    public string $apiVersion = 'v1';

    public string $publicEmailPersonal = 'admin@example.com';
}

$payload = new ApiPayload();

$data = $payload->toArray(snakeCase: true);
// ['apiVersion' => 'v1', 'public_email_personal' => 'admin@example.com']
```

## Documentation

For detailed configuration options and advanced usage.

- 📚 [Installation Guide](docs/installation.md)
- ⚙️ [Configuration Reference](docs/configuration.md)
- 💡 [Usage Examples](docs/examples.md)
- 🧪 [Testing Guide](docs/testing.md)
- 🛠️ [Development Guide](docs/development.md)
- 🔄 [Upgrade Guide](UPGRADE.md)

## Package information

[![PHP](https://img.shields.io/badge/%3E%3D8.1-777BB4.svg?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/releases/8.1/en.php)
[![Latest Stable Version](https://img.shields.io/packagist/v/ui-awesome/model.svg?style=for-the-badge&logo=packagist&logoColor=white&label=Stable)](https://packagist.org/packages/ui-awesome/model)
[![Total Downloads](https://img.shields.io/packagist/dt/ui-awesome/model.svg?style=for-the-badge&logo=composer&logoColor=white&label=Downloads)](https://packagist.org/packages/ui-awesome/model)

## Quality code

[![Codecov](https://img.shields.io/codecov/c/github/ui-awesome/model.svg?style=for-the-badge&logo=codecov&logoColor=white&label=Coverage)](https://codecov.io/github/ui-awesome/model)
[![PHPStan Level Max](https://img.shields.io/badge/PHPStan-Level%20Max-4F5D95.svg?style=for-the-badge&logo=github&logoColor=white)](https://github.com/ui-awesome/model/actions/workflows/static.yml)
[![StyleCI](https://img.shields.io/badge/StyleCI-Passed-44CC11.svg?style=for-the-badge&logo=github&logoColor=white)](https://github.styleci.io/repos/773929534?branch=main)

## Our social networks

[![Follow on X](https://img.shields.io/badge/-Follow%20on%20X-1DA1F2.svg?style=for-the-badge&logo=x&logoColor=white&labelColor=000000)](https://x.com/Terabytesoftw)

## License

[![License](https://img.shields.io/badge/License-BSD--3--Clause-brightgreen.svg?style=for-the-badge&logo=opensourceinitiative&logoColor=white&labelColor=555555)](LICENSE)
