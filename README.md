<p align="center">
    <a href="https://github.com/ui-awesome/model" target="_blank">
        <img src="https://avatars.githubusercontent.com/u/103309199?s%25253D400%252526u%25253Dca3561c692f53ed7eb290d3bb226a2828741606f%252526v%25253D4" height="100px">
    </a>
    <h1 align="center">UIAwesome Model for PHP.</h1>
    <br>
</p>

<p align="center">
    <a href="https://github.com/ui-awesome/model/actions/workflows/build.yml" target="_blank">
        <img src="https://github.com/ui-awesome/model/actions/workflows/build.yml/badge.svg" alt="PHPUnit">
    </a>
    <a href="https://codecov.io/gh/ui-awesome/model" target="_blank">
        <img src="https://codecov.io/gh/ui-awesome/model/branch/main/graph/badge.svg?token=CEBVCYZNQK" alt="Codecov">
    </a>
    <a href="https://dashboard.stryker-mutator.io/reports/github.com/ui-awesome/model/main" target="_blank">
        <img src="https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fui-awesome%2Fmodel%2Fmain" alt="Infection">
    </a>
    <a href="https://github.com/ui-awesome/model/actions/workflows/static.yml" target="_blank">
        <img src="https://github.com/ui-awesome/model/actions/workflows/static.yml/badge.svg" alt="Psalm">
    </a>
    <a href="https://shepherd.dev/github/ui-awesome/model" target="_blank">
        <img src="https://shepherd.dev/github/ui-awesome/model/coverage.svg" alt="Psalm Coverage">
    </a>
    <a href="https://github.styleci.io/repos/773929534?branch=main">
        <img src="https://github.styleci.io/repos/773929534/shield?branch=main" alt="Style ci">
    </a>    
</p>

The UIAwesome Model package provides a robust set of tools for managing data models in PHP applications.

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

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```shell
composer require --prefer-dist ui-awesome/model:"^0.1"
```

or add

```json
"ui-awesome/model": "^0.1"
```

## Usage

[Check the documentation docs](docs/README.md) to learn about usage.

## Testing

[Check the documentation testing](docs/testing.md) to learn about testing.

## Support versions

[![PHP81](https://img.shields.io/badge/PHP-%3E%3D8.1-787CB5)](https://www.php.net/releases/8.1/en.php)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Our social networks

[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/Terabytesoftw)
