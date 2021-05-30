<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\PHPStan\Reflection;

use Forrest79\PhPgSql;

final class FluentQueryExecuteDynamicMethodReturnTypeExtension extends FetchDynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return PhPgSql\Fluent\QueryExecute::class;
	}

}
