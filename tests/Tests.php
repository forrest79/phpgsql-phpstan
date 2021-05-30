<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\Tests;

use Forrest79\PhPgSql\Db;

class Tests
{

	public static function testFluentComplexDynamicMethodReturnTypeExtension(OwnQuery $ownQuery): void
	{
		$ownQuery->ownQueryFunction();

		$complex = $ownQuery->whereAnd();

		$ownQueryFromComplex = $complex->query();

		$ownQueryFromComplex->ownQueryFunction();
	}


	public static function testDbResultDynamicMethodReturnTypeExtension(Db\Result $result): void
	{
		$ownRow = $result->fetch();
		\assert($ownRow !== NULL);

		$ownRow->ownRowFunction();

		foreach ($result->fetchAll() as $ownRowAll) {
			$ownRowAll->ownRowFunction();
		}

		foreach ($result->fetchAssoc('id') as $ownRowAssoc) {
			$ownRowAssoc->ownRowFunction();
		}

		foreach ($result->getIterator() as $ownRowIteration) {
			$ownRowIteration->ownRowFunction();
		}

		// Can't be simple done right now :-(
		//foreach ($result as $ownRowIteration) {
		//	$ownRowIteration->ownRowFunction();
		//}
	}


	public static function testFluentQueryExecuteDynamicMethodReturnTypeExtension(OwnQuery $ownQuery): void
	{
		$ownRow = $ownQuery->fetch();
		\assert($ownRow !== NULL);

		$ownRow->ownRowFunction();

		foreach ($ownQuery->fetchAll() as $ownRowAll) {
			$ownRowAll->ownRowFunction();
		}

		foreach ($ownQuery->fetchAssoc('id') as $ownRowAssoc) {
			$ownRowAssoc->ownRowFunction();
		}

		foreach ($ownQuery->getIterator() as $ownRowIteration) {
			$ownRowIteration->ownRowFunction();
		}

		// Can't be simple done right now :-(
		//foreach ($ownQuery as $ownRowIteration) {
		//	$ownRowIteration->ownRowFunction();
		//}
	}

}
