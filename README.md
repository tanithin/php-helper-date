# php-date-helper

Date format helper library for PHP.

## Installation

### Composer

Add to your composer.json or create a new composer.json:

```js
{
    "require": {
        "tanithin/php-helper-date": "*"
    }
}
```

Tell the composer to download the library by running the command:

```sh
$ php composer.phar install
```

To include using compser require, run the following command from your project.

```sh
$ php composer.phar require tanithin/php-date-helper
```

## Basic usages

### Creating object

```php
use Nexche\Helper\Date;
$builder = new Date() ;
```
or

```php
use Nexche\Helper\Date;
$builder = new Date([
    'user' => [
        'date' => 'd-m-y',
        'datetime' => 'd-m-y h:i a',
        'time' => 'h:i a',
    ]
]) ;
```

The array key becomes the method prefix for Date(), Now(), DateTime() and Time() methods. Here the word "user" can be prefixed.

### Optionally create a static instance

```php
$builder->persist() ;
```

### Retrieve formatted current date time

```php
echo $builder->userDate() ;
echo $builder->userDatetime() ;
echo $builder->userTime() ;
```

or

```php
echo Date::userDate() ;
echo Date::userDatetime() ;
echo Date::userTime() ;
```


### Retrieve formated given datetime

```php
echo $builder->userDate(time()) ;
echo $builder->userDatetime(time()) ;
echo $builder->userTime() ;
```

or

```php
echo Date::userDate(time()) ;
echo Date::userDatetime(time()) ;
echo Date::userTime(time()) ;
```
