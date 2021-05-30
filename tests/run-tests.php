<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\Tests;

require __DIR__ . '/../vendor/autoload.php';

use Forrest79\PhPgSql\Db;
use Forrest79\PhPgSql\Fluent;

// This is to test own PHPStan rules (running this file in PHP will fail and it's OK)

$connection = new Db\Connection();

$ownQuery = new OwnQuery(new Fluent\QueryBuilder(), $connection);

Tests::testFluentComplexDynamicMethodReturnTypeExtension($ownQuery);
Tests::testDbResultDynamicMethodReturnTypeExtension($ownQuery->execute());
Tests::testFluentQueryExecuteDynamicMethodReturnTypeExtension($ownQuery);
