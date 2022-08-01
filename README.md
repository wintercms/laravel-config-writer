# Laravel Config Writer

[![Version](https://img.shields.io/github/v/release/wintercms/laravel-config-writer?sort=semver&style=flat-square)](https://github.com/wintercms/laravel-config-writer/releases)
[![Tests](https://img.shields.io/github/workflow/status/wintercms/laravel-config-writer/Tests/develop?label=tests&style=flat-square)](https://github.com/wintercms/laravel-config-writer/actions)
[![License](https://img.shields.io/github/license/winter/laravel-config-writer?label=open%20source&style=flat-square)](https://packagist.org/packages/winter/laravel-config-writer)
[![Discord](https://img.shields.io/discord/816852513684193281?label=discord&style=flat-square)](https://discord.gg/D5MFSPH6Ux)

A utility to easily create and modify Laravel-style PHP configuration files and environment files whilst maintaining the formatting and comments contained within. This utility works by parsing the configuration files using the [PHP Parser library](https://github.com/nikic/php-parser) to convert the configuration into an abstract syntax tree, then carefully modifying the configuration values as required.

This library was originally written as part of the [Storm library](https://github.com/wintercms/storm) in [Winter CMS](https://wintercms.com), but has since been extracted and repurposed as a standalone library.

## Installation

```
composer require winter/laravel-config-writer
```

## Usage

### PHP array files

You can modify Laravel-style PHP configuration files - PHP files that return a single array - by using the `Winter\LaravelConfigWriter\ArrayFile` class. Use the `open` method to open an existing file for modification, or to create a new config file.

```php
use Winter\LaravelConfigWriter\ArrayFile;

$config = ArrayFile::open(base_path('config/app.php'));
```

You can set values using the `set` method. This method can be used fluently, or can be called with a single key and value or an array of keys and values.

```php
$config->set('name', 'Winter CMS');

$config
    ->set('locale', 'en_US')
    ->set('fallbackLocale', 'en');

$config->set([
    'trustedHosts' => true,
    'trustedProxies' => '*',
]);
```

You can also set deep values in an array value by specifying the key in dot notation, or as a nested array.

```php
$config->set('connections.mysql.host', 'localhost');

$config->set([
    'connections' => [
        'sqlite' => [
            'database' => 'database.sqlite',
            'driver' => 'sqlite',
            'foreign_key_constraints' => true,
            'prefix' => '',
            'url' => null,
        ],
    ],
]);
```

To finalise all your changes, use the `write` method to write the changes to the open file.

```php
$config->write();
```

If desired, you may also write the changes to another file altogether.

```php
$config->write('path/to/newfile.php');
```

Or you can simply render the changes as a string.

```php
$config->render();
```

#### Function calls as values

Function calls can be added to your configuration file by using the `function` method. The first parameter of the `function` method defines the function to call, and the second parameter accepts an array of parameters to provide to the function.

```php
$config->set('name', $config->function('env', ['APP_NAME', 'Winter CMS']));
```

#### Constants as values

Constants can be added to your configuration file by using the `constant` method. The only parameter required is the name of the constant.

```php
$config->set('foo.bar', $config->constant('My\Class::CONSTANT'));
```

#### Sorting the configuration file

You can sort the configuration keys alphabetically by using the `sort` method. This will sort all current configuration values.

```php
$config->sort();
```

By default, this will sort the keys alphabetically in ascending order. To sort in the opposite direction, include the `ArrayFile::SORT_DESC` parameter.

```php
$config->sort(ArrayFile::SORT_DESC);
```

### Environment files

This utility library also allows manipulation of environment files, typically found as `.env` files in a project. The `Winter\LaravelConfigWriter\EnvFile::open()` method allows you to open or create an environment file for modification.

```php
use Winter\LaravelConfigWriter\EnvFile;

$config = EnvFile::open(base_path('.env'));
```

You can set values using the `set` method. This method can be used fluently, or can be called with a single key and value or an array of keys and values.

```php
$config->set('APP_NAME', 'Winter CMS');

$config
    ->set('APP_URL', 'https://wintercms.com')
    ->set('APP_ENV', 'production');

$config->set([
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => 'database.sqlite',
]);
```

> **Note:** Arrays are not supported in environment files.

You can add an empty line into the environment file by using the `addEmptyLine` method. This allows you to separate groups of environment variables.

```php
$env->set('FOO', 'bar');
$env->addEmptyLine();
$env->set('BAR', 'foo');
```

To finalise all your changes, use the `write` method to write the changes to the open file.

```php
$config->write();
```

If desired, you may also write the changes to another file altogether.

```php
$config->write(base_path('.env.local'));
```

Or you can simply render the changes as a string.

```php
$config->render();
```

## License

This utility library is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Security vulnerabilities

Please review our [security policy](https://github.com/wintercms/winter/security/policy) on how to report security vulnerabilities.
