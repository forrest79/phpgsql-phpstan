<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\Tests;

use Forrest79\PhPgSql\Db;

class OwnRow extends Db\Row
{

	public function ownRowFunction(): void
	{
		// Just to test PHPStan rule...
	}

}
