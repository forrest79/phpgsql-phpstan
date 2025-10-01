# PhPgSql class reflection extension for PHPStan

[![Latest Stable Version](https://poser.pugx.org/forrest79/phpgsql-phpstan/v)](//packagist.org/packages/forrest79/phpgsql-phpstan)
[![Monthly Downloads](https://poser.pugx.org/forrest79/phpgsql-phpstan/d/monthly)](//packagist.org/packages/forrest79/phpgsql-phpstan)
[![License](https://poser.pugx.org/forrest79/phpgsql-phpstan/license)](//packagist.org/packages/forrest79/phpgsql-phpstan)
[![Build](https://github.com/forrest79/phpgsql-phpstan/actions/workflows/build.yml/badge.svg?branch=master)](https://github.com/forrest79/phpgsql-phpstan/actions/workflows/build.yml)

* [PhPgSql](https://github.com/forrest79/phpgsql)
* [PHPStan](https://github.com/phpstan/phpstan)

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

If you're using your own `Forrest79\PhPgSql\Db\Row` or `Forrest79\PhPgSql\Fluen\Query`, you can set it likes this:

```yaml
parameters:
    forrest79:
        phpgsql:
            dbRowClass: MyOwn\PhPgSql\Db\RowXyz
            fluentQueryClass: MyOwn\PhPgSql\Fluent\QueryXyz
```

Or you can set just one extension:

- for `PhPgSql\Db\Result` (for fetching the correct `Row` object from fetch methods):

```yaml
services:
    Forrest79PhPgSqlPHPStanReflectionDbResultDynamicMethodReturnTypeExtension:
        arguments:
            dbRowClass: MyOwn\PhPgSql\Db\RowXyz
```
- for `PhPgSql\Fluent\QueryExecute` (for fetching the correct `Row` object from fetch methods):

```yaml
services:
    Forrest79PhPgSqlPHPStanReflectionFluentQueryExecuteDynamicMethodReturnTypeExtension:
        arguments:
            dbRowClass: MyOwn\PhPgSql\Db\RowXyz
```

- for `PhPgSql\Fluent\Condition` (to return right `Query` in `query()` method):

```yaml
services:
    Forrest79PhPgSqlPHPStanReflectionFluentConditionDynamicMethodReturnTypeExtension:
        arguments:
            fluentQueryClass: MyOwn\PhPgSql\Fluent\QueryXyz
```
