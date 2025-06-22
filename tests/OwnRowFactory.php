<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\Tests;

use Forrest79\PhPgSql\Db;

final class OwnRowFactory implements Db\RowFactory
{

	/**
	 * @param array<string, string|null> $rawValues
	 */
	public function createRow(Db\ColumnValueParser $columnValueParser, array $rawValues): OwnRow
	{
		return new OwnRow($columnValueParser, $rawValues);
	}

}
