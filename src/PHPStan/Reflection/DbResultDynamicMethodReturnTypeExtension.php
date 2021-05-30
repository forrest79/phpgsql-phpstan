<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\PHPStan\Reflection;

use Forrest79\PhPgSql;

final class DbResultDynamicMethodReturnTypeExtension extends FetchDynamicMethodReturnTypeExtension
{

	public function getClass(): string
	{
		return PhPgSql\Db\Result::class;
	}

}
