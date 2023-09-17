<?php declare(strict_types=1);

if (!function_exists('is_dbrow')) {

	/**
	 * @param array<string, string>|NULL $expectedProperties
	 */
	function is_dbrow(mixed $row, array|NULL $expectedProperties = NULL): bool
	{
		if (!($row instanceof Forrest79\PhPgSql\Db\Row)) {
			return FALSE;
		}

		if ($expectedProperties !== NULL) {
			$extraColumns = array_diff(array_keys($expectedProperties), $row->getColumns());
			if ($extraColumns !== []) {
				throw new Forrest79\PhPgSql\PHPStan\Exceptions\MissingColumnException(sprintf('These expected properties are missing in row columns: %s', implode(', ', $extraColumns)));
			}
		}

		return TRUE;
	}

}
