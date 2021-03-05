# UberCache for Laravel

[![Author](http://img.shields.io/badge/follow-@kielabokkie-blue.svg?logo=twitter&style=flat-square)](https://twitter.com/kielabokkie)
[![Packagist Version](https://img.shields.io/packagist/v/kielabokkie/ubercache.svg?style=flat-square)](https://packagist.org/packages/kielabokkie/ubercache)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

UberCache for Laravel works very similar to the [Retrieve & Store](https://laravel.com/docs/8.x/cache#retrieve-store) cache functionality of Laravel. The difference is that once Laravel's cache expires, and the retrieval of new data failed, you are left with no data at all. This is where UberCache comes in, as it allows you to reuse your old cache in case retrieving of new data failed.

## Sponsor Me

If you find this package useful, or it somehow sparks joy, please consider [sponsoring me](https://github.com/sponsors/kielabokkie).

## Requirements

* PHP >= 7.4
* Laravel 6.x and higher

## Installation

Install the package via composer:

```bash
composer require kielabokkie/uber-cache
```

## TLDR

```php
// data is fetched from the external API and cached for 1 minute
$todo = UberCache::remember('key', now()->addMinute(), now()->addHour(), function () {
    return Http::get('https://jsonplaceholder.typicode.com/todos/1')->json();
});

dump($todo);

/**
{
    id: 1,
    title: "delectus aut autem",
    completed: false
}
*/

// -- 5 minutes later --

// cache is expired so should fetch from API but API is down
$todo = UberCache::remember('key', now()->addMinute(), now()->addHour(), function () {
    throw new \Exception('API error');
});

// the todo still returns the previously expired cache
dump($todo);

/**
{
    id: 1,
    title: "delectus aut autem",
    completed: false
}
*/

// -- 2 hours later --

// cache is expired and max cache time also expired but API is still down
$todo = UberCache::remember('key', now()->addMinute(), now()->addHour(), function () {
    throw new \Exception('API error');
});

// an UberCacheException is thrown
```

## Usage

As mentioned before, UberCache provides just one function that is very similar to Laravel's `remember` cache function. Let's start by looking at an example of this first:

```php
$value = Cache::remember('todos', now()->addMinute(), function () {
    return Http::get('https://jsonplaceholder.typicode.com/todos/1')->json();
});
```

As you can see it takes 3 parameters: the cache key, the lifetime of the value in your cache and a callback function that's responsible for retrieving the data.

Now let's look at UberCache's remember function, it takes one extra parameter before the callback called `maxTtl`. This is used to set the maximum time your cache is allowed to be used.

```php
$value = UberCache::remember('todos', now()->addMinute(), now()->addHour(), function () {
    return Http::get('https://jsonplaceholder.typicode.com/todos/1')->json();
});
```

It's clear that it's almost identical to Laravel's remember function but thanks to the `maxTtl` parameter our cache is a little smarter. The example above will cache the todo that is fetched for 1 minute. If this function is called again after more than 1 minute the cache is expired, so the API call is executed again. If the API call fails for whatever reason, maybe it is down temporary, UberCache will restore the old cached value and continue as without breaking.

If the API calls keep on failing and no new data can be fetched within the time set as the `maxTtl` then an exception will be thrown. This is to ensure you are not working with old cache data forever without being aware of it.
