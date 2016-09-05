# Memcached SASL extension for Laravel5

forked from [ripples-alive/Memcached-sasl](https://github.com/ripples-alive/Memcached-sasl)

PHP长连接memcached, lumen可用
参考 [阿里云官方文档](https://help.aliyun.com/document_detail/26554.html), [laravel-memcached-plus](https://github.com/b3it/laravel-memcached-plus) 做了一些修改.
尚未提交 composer

This is a custom cache extension of memcached sasl for laravel5, especially for aliyun ocs.

## Installation

This package can be installed through `composer`.

```bash
composer require ripples/memcached-sasl
```

## Usage

In order to use the extension, the service provider must be registered.

```php
// bootstrap/app.php
$app->register(Ripples\Memcached\MemcachedSaslServiceProvider::class);
```

Finally, add a store to you config file `cache.php` and update cache driver to `memcached_sasl`.

```php
return [
    'default' => 'memcached_sasl',

    'stores' => [
        'memcached_sasl' => [
            'driver' => 'memcached_sasl',
            'persistent_id' => env('MEMCACHED_PERSISTENT', 'lumen'),
            'options'    => [
                Memcached::OPT_NO_BLOCK         => true,
                Memcached::OPT_AUTO_EJECT_HOSTS => true,
                Memcached::OPT_CONNECT_TIMEOUT  => 2000,
                Memcached::OPT_POLL_TIMEOUT     => 2000,
                Memcached::OPT_RETRY_TIMEOUT    => 2,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100
                ]
            ],
            'auth' => [
                'username' => env('MEMCACHED_USERNAME', ''),
                'password' => env('MEMCACHED_PASSWORD', '')
            ]
        ],
    ]
]
```

## LICENSE

[MIT](./LICENSE)
