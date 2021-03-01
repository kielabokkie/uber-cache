# UberCache for Laravel

[![Author](http://img.shields.io/badge/follow-@kielabokkie-blue.svg?logo=twitter&style=flat-square)](https://twitter.com/kielabokkie)
[![Packagist Version](https://img.shields.io/packagist/v/kielabokkie/ubercache.svg?style=flat-square)](https://packagist.org/packages/kielabokkie/ubercache)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

UberCache for Laravel provides a single function that allows you to use your old cache in case fetching of new data failed for some reason.

## Requirements

* PHP >= 7.4
* Laravel 6.x and up

## Installation

Install the package via composer:

```bash
composer require kielabokkie/uber-cache
```

## Usage

UberCache works very similar to the [Retrieve & Store](https://laravel.com/docs/8.x/cache#retrieve-store) functionality of Laravel.

The `remember()` function takes 4 parameters:

| Parameter | Description                                  |
| --------- | -------------------------------------------- |
| key       | The cache key                                |
| ttl       | The time you want the value to be cached     |
| maxTtl    | The maximum time you want to cache the value |
| callback  | The function that gets the data              |

```php
$result = UberCache::remember('test', now()->addMinutes(5), now()->addHour(), function () {
    return 11;
});
```
