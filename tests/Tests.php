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

		foreach ($result->fetchAssoc('id') as $id => $ownRowAssoc) {
			self::checkIntString($id);
			$ownRowAssoc->ownRowFunction();
		}

		foreach ($result->fetchAssoc('[]') as $index => $ownRowAssoc) {
			self::checkInt($index);
			$ownRowAssoc->ownRowFunction();
		}

		foreach ($result->fetchAssoc('type[]') as $type => $listTypes) {
			self::checkIntString($type);
			self::checkList($listTypes);
			foreach ($listTypes as $index => $ownRowAssoc) {
				self::checkInt($index);
				$ownRowAssoc->ownRowFunction();
			}
		}

		foreach ($result->fetchAssoc('[]type') as $index => $types) {
			self::checkInt($index);
			foreach ($types as $type => $ownRowAssoc) {
				self::checkIntString($type);
				$ownRowAssoc->ownRowFunction();
			}
		}

		self::checkList($result->fetchAssoc('[]type'));

		foreach ($result->fetchAssoc('type|subtype') as $type => $subtypes) {
			self::checkIntString($type);
			foreach ($subtypes as $subtype => $ownRowAssoc) {
				self::checkIntString($subtype);
				$ownRowAssoc->ownRowFunction();
			}
		}

		foreach ($result->fetchAssoc('country[]city') as $country => $listCities) {
			self::checkIntString($country);
			self::checkList($listCities);
			foreach ($listCities as $index => $cities) {
				self::checkInt($index);
				foreach ($cities as $city => $ownRowAssoc) {
					self::checkIntString($city);
					$ownRowAssoc->ownRowFunction();
				}
			}
		}

		foreach ($result->fetchAssoc('country[]city=id') as $country => $listCities) {
			self::checkIntString($country);
			self::checkList($listCities);
			foreach ($listCities as $index => $cities) {
				self::checkInt($index);
				foreach ($cities as $city => $id) {
					self::checkIntString($city);
					self::checkIntString($id);
				}
			}
		}

		foreach ($result->fetchAssoc('country[]city=[]') as $country => $listCities) {
			self::checkIntString($country);
			self::checkList($listCities);
			foreach ($listCities as $index => $cities) {
				self::checkInt($index);
				foreach ($cities as $city => $array) {
					self::checkIntString($city);
					self::checkArray($array);
				}
			}
		}

		foreach ($result->fetchAssoc('id|country[]city=[]') as $id => $countries) {
			self::checkIntString($id);
			foreach ($countries as $country => $listCities) {
				self::checkIntString($country);
				self::checkList($listCities);
				foreach ($listCities as $index => $cities) {
					self::checkInt($index);
					foreach ($cities as $city => $array) {
						self::checkIntString($city);
						self::checkArray($array);
					}
				}
			}
		}

		foreach ($result->fetchPairs() as $key => $value) {
			self::checkIntString($key);
		}

		foreach ($result->fetchPairs('column1', 'column2') as $key => $value) {
			self::checkIntString($key);
		}

		self::checkList($result->fetchPairs(NULL, 'column'));

		foreach ($result->getIterator() as $ownRowIteration) {
			$ownRowIteration->ownRowFunction();
		}

		foreach ($result->fetchIterator() as $ownRowIteration) {
			$ownRowIteration->ownRowFunction();
		}
	}


	private static function	checkInt(int $int): void
	{
		echo 'This is int: ' . $int;
	}


	private static function	checkIntString(int|string $intString): void
	{
		echo 'This is int or string: ' . $intString;
	}


	/**
	 * @param array<int|string, mixed> $array
	 */
	private static function	checkArray(array $array): void
	{
		echo 'This is array: ' . implode(', ', $array);
	}


	/**
	 * @param list<mixed> $list
	 */
	private static function	checkList(array $list): void
	{
		echo 'This is list: ' . implode(', ', $list);
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

		foreach ($ownQuery->fetchIterator() as $ownRowIteration) {
			$ownRowIteration->ownRowFunction();
		}
	}


	public static function testIsDbRowFunctionTypeSpecifyingExtension(OwnQuery $ownQuery): void
	{
		foreach ($ownQuery as $ownRowIteration) {
			assert(is_dbrow($ownRowIteration));
			$ownRowIteration->ownRowFunction();
		}

		$row = $ownQuery->fetch();
		assert(is_dbrow($row, ['columnInt' => '?int', 'columnString' => 'string', 'columnFloat' => 'float', 'columnDatetime' => \DateTime::class]));
		self::testTypes($row->columnInt, $row->columnString, $row->columnFloat, $row->columnDatetime);
	}


	private static function testTypes(
		int|NULL $nullableInteger,
		string $text,
		float $numeric,
		\DateTime $dateTime,
	): void
	{
		var_dump($nullableInteger, $text, $nullableInteger, $numeric, $dateTime);
	}


	public static function testIsDbRowFunctionTypeSpecifyingExtension2(Db\Row $row): void
	{
		/** @var DbRow $row */
		$row->ownRowFunction();
	}

}
