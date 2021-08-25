## Basic Auth PSR-15 middleware for Symfony framework.

![test](https://github.com/kafkiansky/symfony-basic-auth-middleware/workflows/test/badge.svg?event=push)
[![Codecov](https://codecov.io/gh/kafkiansky/symfony-basic-auth-middleware/branch/master/graph/badge.svg)](https://codecov.io/gh/kafkiansky/symfony-basic-auth-middleware)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/kafkiansky/symfony-basic-auth-middleware.svg?style=flat-square)](https://packagist.org/packages/kafkiansky/symfony-basic-auth-middleware)
[![Quality Score](https://img.shields.io/scrutinizer/g/kafkiansky/symfony-basic-auth-middleware.svg?style=flat-square)](https://scrutinizer-ci.com/g/kafkiansky/symfony-basic-auth-middleware)

### Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)
- [License](#license)


## Installation


```bash
composer require kafkiansky/symfony-basic-auth-middleware
```

## Configuration

You can configure user, password, realm and excluded paths, if you want:

```yaml
## services.yaml

services:
   ...

    Kafkiansky\SymfonyMiddleware\AuthenticateBasic:
      arguments:
          $user: '%env(BASIC_HTTP_AUTH_USER)%'
          $password: '%env(BASIC_HTTP_AUTH_PASSWD)%'
          $realm: 'my-app'
          $excludedPaths: ## this is optional
              - '/test'
          $excludedPatterns: ## and this is optional
              - '/posts\/\\d+\\/edit/'
```

## Usage

### Use as single middleware

```php
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;

final class SomeController
{
    #[Middleware([Kafkiansky\SymfonyMiddleware\AuthenticateBasic::class])]
    public function index()
    {}
}
```

### Use as global middleware

```yaml
## symmidleware.yaml

symiddleware:
    global:
        - Kafkiansky\SymfonyMiddleware\AuthenticateBasic
```

### Use as group middleware

```yaml
## symmidleware.yaml

symiddleware:
    groups:
       web:
        - Kafkiansky\SymfonyMiddleware\AuthenticateBasic
```

```php
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;

final class SomeController
{
    #[Middleware(['web'])]
    public function index()
    {}
}
```

```php
use Kafkiansky\SymfonyMiddleware\Attribute\Middleware;

#[Middleware(['web'])]
final class SomeController
{
    public function index()
    {}
}
```

## Testing

``` bash
$ composer test
```  

## License

The MIT License (MIT). See [License File](LICENSE.md) for more information.
