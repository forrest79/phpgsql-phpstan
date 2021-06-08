<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\Tests;

use Forrest79\PhPgSql\Fluent;

final class OwnQuery extends Fluent\QueryExecute
{

	public function ownQueryFunction(): void
	{
		// Just to test PHPStan rule...
	}

}
