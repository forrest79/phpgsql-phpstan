# PhPgSql class reflection extension for PHPStan

[![License](https://img.shields.io/badge/License-BSD%203--Clause-blue.svg)](https://github.com/forrest79/PhPgSql-PHPStan/blob/master/license.md)
[![Build](https://github.com/forrest79/PhPgSql-PHPStan/actions/workflows/build.yml/badge.svg?branch=master)](https://github.com/forrest79/PhPgSql-PHPStan/actions/workflows/build.yml)

* [PHPStan](https://github.com/phpstan/phpstan)
* [PhPgSql](https://github.com/forrest79/PhPgSql)

## Introduction

This extension defines dynamic methods and other PHPStan setting for `Forrest79\PhPgSql`.

## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

```
composer require --dev forrest79/phpgsql-phpstan
```

## Using

Include `extension.neon` in your project's PHPStan config:

```yaml
includes:
    - vendor/forrest79/phpgsql-phpstan/extension.neon
```

If you're using your own `Forrest79\PhPgSql\Fluen\Query` set it like this:

```yaml
services:
    Forrest79PhPgSqlPHPStanReflectionFluentComplexDynamicMethodReturnTypeExtension:
        arguments:
            fluentQueryClass: MyOwn\PhPgSql\Fluent\QueryXyz
```
