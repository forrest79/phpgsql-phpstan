<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\Tests;

use Forrest79\PhPgSql\Db;

final class Tests
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

		$checkInt = static function (int $int): void {
			echo 'This is int: ' . $int;
		};

		/**
		 * @param int|string $intString
		 */
		$checkIntString = static function ($intString): void {
			echo 'This is int or string: ' . $intString;
		};

		$checkArray = static function (array $array): void {
			echo 'This is arr: ' . implode(', ', $array);
		};

		foreach ($result->fetchAssoc('id') as $id => $ownRowAssoc) {
			$checkIntString($id);
			$ownRowAssoc->ownRowFunction();
		}

		foreach ($result->fetchAssoc('[]') as $index => $ownRowAssoc) {
			$checkInt($index);
			$ownRowAssoc->ownRowFunction();
		}

		foreach ($result->fetchAssoc('type[]') as $type => $types) {
			$checkIntString($type);
			foreach ($types as $index => $ownRowAssoc) {
				$checkInt($index);
				$ownRowAssoc->ownRowFunction();
			}
		}

		foreach ($result->fetchAssoc('[]type') as $index => $type) {
			$checkInt($index);
			foreach ($type as $ownRowAssoc) {
				$checkIntString($type);
				$ownRowAssoc->ownRowFunction();
			}
		}

		foreach ($result->fetchAssoc('type|subtype') as $type => $subtypes) {
			$checkIntString($type);
			foreach ($subtypes as $subtype => $ownRowAssoc) {
				$checkIntString($subtype);
				$ownRowAssoc->ownRowFunction();
			}
		}

		foreach ($result->fetchAssoc('country[]city') as $country => $indexesCities) {
			$checkIntString($country);
			foreach ($indexesCities as $index => $cities) {
				$checkInt($index);
				foreach ($cities as $city => $ownRowAssoc) {
					$checkIntString($city);
					$ownRowAssoc->ownRowFunction();
				}
			}
		}

		foreach ($result->fetchAssoc('country[]city=id') as $country => $indexesCities) {
			$checkIntString($country);
			foreach ($indexesCities as $index => $cities) {
				$checkInt($index);
				foreach ($cities as $city => $id) {
					$checkIntString($city);
					$checkIntString($id);
				}
			}
		}

		foreach ($result->fetchAssoc('country[]city=[]') as $country => $indexesCities) {
			$checkIntString($country);
			foreach ($indexesCities as $index => $cities) {
				$checkInt($index);
				foreach ($cities as $city => $array) {
					$checkIntString($city);
					$checkArray($array);
				}
			}
		}

		foreach ($result->fetchAssoc('id|country[]city=[]') as $id => $countries) {
			$checkIntString($id);
			foreach ($countries as $country => $indexesCities) {
				$checkIntString($country);
				foreach ($indexesCities as $index => $cities) {
					$checkInt($index);
					foreach ($cities as $city => $array) {
						$checkIntString($city);
						$checkArray($array);
					}
				}
			}
		}

		foreach ($result->getIterator() as $ownRowIteration) {
			$ownRowIteration->ownRowFunction();
		}

		// Can't be simply done right now :-(
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

		// Can't be simple done right now :-( Use `assert(is_dbrow(...));`
		//foreach ($ownQuery as $ownRowIteration) {
		//	$ownRowIteration->ownRowFunction();
		//}
	}


	public static function testIsDbRowFunctionTypeSpecifyingExtension(OwnQuery $ownQuery): void
	{
		foreach ($ownQuery as $ownRowIteration) {
			\assert(\is_dbrow($ownRowIteration));
			$ownRowIteration->ownRowFunction();
		}

		$row = $ownQuery->fetch();
		\assert(\is_dbrow($row, ['columnInt' => '?int', 'columnString' => 'string', 'columnFloat' => 'float', 'columnDatetime' => \DateTime::class]));
		self::testTypes($row->columnInt, $row->columnString, $row->columnFloat, $row->columnDatetime);
	}


	private static function testTypes(?int $nullableInteger, string $text, float $numeric, \DateTime $dateTime): void
	{
		var_dump($nullableInteger, $text, $nullableInteger, $numeric, $dateTime);
	}

}
