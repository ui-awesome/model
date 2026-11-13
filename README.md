<!-- markdownlint-disable MD041 -->
<p align="center">
    <a href="https://github.com/ui-awesome/model" target="_blank">
        <img src="https://raw.githubusercontent.com/ui-awesome/.github/refs/heads/main/logo/ui_awesome.png" alt="UI Awesome" width="25%">
    </a>
    <h1 align="center">Model for PHP</h1>
    <br>
</p>
<!-- markdownlint-enable MD041 -->

<p align="center">
    <a href="https://github.com/ui-awesome/model/actions/workflows/build.yml" target="_blank">
        <img src="https://img.shields.io/github/actions/workflow/status/ui-awesome/model/build.yml?style=for-the-badge&label=PHPUnit&logo=github" alt="PHPUnit">
    </a>
    <a href="https://codecov.io/github/ui-awesome/model" target="_blank">
        <img src="https://img.shields.io/codecov/c/github/ui-awesome/model.svg?style=for-the-badge&logo=codecov&logoColor=white&label=Coverage" alt="Coverage">
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
    <em>Nested properties, explicit input mapping, runtime defaults, custom casting, and selective key serialization</em>
</p>

## Features

<picture>
    <source media="(max-width: 767px)" srcset="./docs/svgs/features-mobile.svg">
    <img src="./docs/svgs/features.svg" alt="Feature Overview" style="width: 100%;">
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

use UIAwesome\Model\Attribute\{Cast, DefaultValue, MapFrom, NoSnakeCase, Timestamp, Trim};
use UIAwesome\Model\BaseModel;

final class User extends BaseModel
{
    #[NoSnakeCase]
    public string $apiVersion = 'v1';

    #[DefaultValue('Guest')]
    public string $displayName = '';

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
            'displayName' => '',
            'name' => '  Ada Lovelace  ',
            'tags' => 'php, yii2, model',
            'user-email-address' => 'ada@example.com',
        ],
    ],
);

$types = $model->getTypes();
/*
[
    'apiVersion' => 'string',
    'displayName' => 'string',
    'name' => 'string',
    'email' => 'string',
    'tags' => 'array',
    'updatedAt' => 'timestamp',
]
*/
$payload = $model->toArray(snakeCase: true, exceptProperties: ['updatedAt']);
/*
[
    'apiVersion' => 'v2',
    'display_name' => 'Guest',
    'name' => 'Ada Lovelace',
    'email' => 'ada@example.com',
    'tags' => ['php', 'yii2', 'model'],
]
*/
```

## Explicit payload mapping with `MapFrom`

Use `#[MapFrom('external-key')]` when incoming payload keys do not follow snake_case or camelCase naming.

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\Attribute\MapFrom;
use UIAwesome\Model\BaseModel;

final class JsonLdPayload extends BaseModel
{
    #[MapFrom('@context')]
    public string $context = '';
}

$payload = new JsonLdPayload();

$payload->setValues(['@context' => 'https://schema.org']);
```

## Automatic input trimming with `Trim`

Use `#[Trim]` to normalize leading and trailing spaces for string values during assignment.

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

$profile = new Profile();

$profile->setValues(['display_name' => '  Ada Lovelace  ']);
```

## Forced custom casting with `Cast`

Use `#[Cast('array')]` to transform transport formats such as comma-separated strings.

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

$filter = new SearchFilter();

$filter->setValue('tags', 'php, yii2, model');
```

## Runtime fallback with `DefaultValue`

Use `#[DefaultValue(...)]` to apply a fallback when input values are `null` or `''`.

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

$profile = new Profile();

$profile->setValue('displayName', '');
// 'Guest'
```

## Preserve selected output keys with `NoSnakeCase`

Use `#[NoSnakeCase]` to keep specific property names unchanged when serializing with `snakeCase: true`.

```php
<?php

declare(strict_types=1);

namespace App\Model;

use UIAwesome\Model\Attribute\NoSnakeCase;
use UIAwesome\Model\BaseModel;

final class ApiPayload extends BaseModel
{
    #[NoSnakeCase]
    public string $apiVersion = 'v1';

    public string $publicEmailPersonal = 'admin@example.com';
}

$payload = new ApiPayload();

$data = $payload->toArray(snakeCase: true);
/*
[
    'apiVersion' => 'v1',
    'public_email_personal' => 'admin@example.com',
]
*/
```

## Documentation

For detailed configuration options and advanced usage.

- 📚 [Installation Guide](docs/installation.md)
- ⚙️ [Configuration Reference](docs/configuration.md)
- 💡 [Usage Examples](docs/examples.md)
- 🧪 [Testing Guide](docs/testing.md)
- 🔄 [Upgrade Guide](UPGRADE.md)

## AI assistance

AI agents: read the [documentation index](https://yii2-framework.github.io/docs/llms.txt) and fetch the Markdown version
of a page by appending `index.md` to its URL. The
[AI agents guide](https://yii2-framework.github.io/docs/project/ai-agents/) covers skill installation, client setup and
verification.

- 🤖 [`ui-awesome-model` skill](docs/agents.md): typed input mapping, property attributes, nested values with dot
  notation and model serialization.

Match the installed package revision. If hosted documentation does not match or is unavailable, use the
[repository guides](#documentation), the [skill source](skills/ui-awesome-model/SKILL.md), the source PHPDoc and
the [changelog](CHANGELOG.md). This package is framework-independent; it is not a database model or a validation layer.

## Package information

[![PHP](https://img.shields.io/badge/%3E%3D8.3-777BB4.svg?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/releases/8.3/en.php)
[![Latest Stable Version](https://img.shields.io/packagist/v/ui-awesome/model.svg?style=for-the-badge&logo=packagist&logoColor=white&label=Stable)](https://packagist.org/packages/ui-awesome/model)
[![Total Downloads](https://img.shields.io/packagist/dt/ui-awesome/model.svg?style=for-the-badge&logo=composer&logoColor=white&label=Downloads)](https://packagist.org/packages/ui-awesome/model)

## Project status

[![PHPStan Level Max](https://img.shields.io/badge/PHPStan-Level%20Max-4F5D95.svg?style=for-the-badge&logo=github&logoColor=white)](https://github.com/ui-awesome/model/actions/workflows/static.yml)
[![Quality](https://img.shields.io/github/actions/workflow/status/ui-awesome/model/quality.yml?style=for-the-badge&label=Quality&logo=github)](https://github.com/ui-awesome/model/actions/workflows/quality.yml)
[![StyleCI](https://img.shields.io/badge/dynamic/xml?url=https%3A%2F%2Fgithub.styleci.io%2Frepos%2F773929534%2Fshield%3Fbranch%3Dmain&query=%2F%2F*%5Blocal-name%28%29%3D%27text%27%5D%5B2%5D&label=StyleCI&style=for-the-badge&logo=github&logoColor=white&color=4F5D95)](https://github.styleci.io/repos/773929534?branch=main)

## Security

[![OpenSSF Scorecard](https://api.scorecard.dev/projects/github.com/ui-awesome/model/badge?style=for-the-badge)](https://scorecard.dev/viewer/?uri=github.com/ui-awesome/model)
[![Zizmor](https://img.shields.io/github/actions/workflow/status/ui-awesome/model/security.yml?style=for-the-badge&label=Zizmor&logo=github)](https://github.com/ui-awesome/model/actions/workflows/security.yml)

## Built with AI

[![Built with Claude Code](https://img.shields.io/badge/Built%20with-Claude%20Code-D97757.svg?style=for-the-badge&logo=claude&logoColor=white)](https://claude.com/claude-code)
[![Built with Codex](https://img.shields.io/badge/Built%20with-Codex-10A37F.svg?style=for-the-badge&logo=openaigym&logoColor=white)](https://openai.com/codex)
[![Reviewed by CodeRabbit](https://img.shields.io/badge/Reviewed%20by-CodeRabbit-FF570A.svg?style=for-the-badge&logo=coderabbit&logoColor=white)](https://www.coderabbit.ai)

## Our social networks

[![Follow on X](https://img.shields.io/badge/-Follow%20on%20X-1DA1F2.svg?style=for-the-badge&logo=x&logoColor=white&labelColor=000000)](https://x.com/Terabytesoftw)
[![Follow on Facebook](https://img.shields.io/badge/-Follow%20on%20Facebook-1877F2.svg?style=for-the-badge&logo=facebook&logoColor=white&labelColor=000000)](https://www.facebook.com/wilmer.arambula.9)

## License

[![License](https://img.shields.io/badge/License-BSD--3--Clause-brightgreen.svg?style=for-the-badge&logo=opensourceinitiative&logoColor=white&labelColor=555555)](LICENSE)
