# api

[![Latest Version](https://img.shields.io/github/release/j4k/api.svg?style=flat-square)](https://github.com/j4k/api/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/j4k/api/master.svg?style=flat-square)](https://travis-ci.org/j4k/api)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/j4k/api.svg?style=flat-square)](https://scrutinizer-ci.com/g/j4k/api/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/j4k/api.svg?style=flat-square)](https://scrutinizer-ci.com/g/j4k/api)
[![Total Downloads](https://img.shields.io/packagist/dt/j4k/api.svg?style=flat-square)](https://packagist.org/packages/j4k/api)

This is a helper toolkit for laravel5 that takes a bit of the legwork out of creating json-api compliant api's. It features a host of middleware and response utilities that can be used together or singularly when building out an API to the jsonapi.org specification.

## Todo

- Config Publish
- Proper JSON Construction
- URL Generation
- User Auth with oAuth and WebTokens
- Auth Validation Middleware
- Doc Generation

## Install

Via Composer

``` bash
$ composer require j4k/api
```

## Usage

``` php
$Api = new j4k\Api();
echo $Api->echoPhrase('Hello, j4k!');
```

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email jw@jack.gd instead of using the issue tracker.

## Credits

- [j4k](https://github.com/j4k)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
