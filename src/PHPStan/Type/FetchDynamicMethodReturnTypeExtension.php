<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\PHPStan\Type;

use Forrest79\PhPgSql;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type;
use PhpParser\Node\Expr\MethodCall;

abstract class FetchDynamicMethodReturnTypeExtension implements Type\DynamicMethodReturnTypeExtension
{
	/** @var Type\ObjectType */
	private $dbRowObjectType;


	public function __construct(string $dbRowClass)
	{
		$this->dbRowObjectType = new Type\ObjectType($dbRowClass);
	}


	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return \in_array($methodReflection->getName(), ['fetch', 'fetchAll', 'fetchAssoc', 'fetchPairs', 'getIterator'], TRUE);
	}


	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type\Type
	{
		if ($methodReflection->getName() === 'fetch') {
			return new Type\UnionType([$this->dbRowObjectType, new Type\NullType()]);
		} else if ($methodReflection->getName() === 'fetchAll') {
			return Type\Accessory\AccessoryArrayListType::intersectWith(new Type\ArrayType(new Type\IntegerType(), $this->dbRowObjectType));
		} else if ($methodReflection->getName() === 'fetchAssoc') {
			if (count($methodCall->getArgs()) > 0) {
				$keyArg = $methodCall->getArgs()[0]->value;
				$keyScopedType = $scope->getType($keyArg);

				// we're able to parse only constant string types, return default
				$constantStrings = $keyScopedType->getConstantStrings();
				if (count($constantStrings) === 0) {
					return new Type\ArrayType(
						new Type\UnionType([new Type\IntegerType(), new Type\StringType()]),
						new Type\MixedType()
					);
				}

				$assocDesc = PhPgSql\PHPStan\Helper::getImplodedConstantString($constantStrings);

				$parts = \preg_split('#(\[\]|=|\|)#', $assocDesc, -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);
				if (($parts === FALSE) || ($parts === [])) {
					return new Type\ErrorType(); // little hack, exception is thrown in fetchAssoc() - this should be nicer as PHPStan rule, but we need to care about this here too
				}

				$firstPart = \reset($parts);
				$lastPart = \end($parts);
				if (($firstPart === '=') || ($firstPart === '|') || ($lastPart === '=') || ($lastPart === '|')) {
					return new Type\ErrorType(); // little hack, exception is thrown in fetchAssoc() - this should be nicer as PHPStan rule, but we need to care about this here too
				}

				$reversedParts = array_reverse($parts);
				if ((count($reversedParts) >= 2) && ($reversedParts[1] === '=')) {
					if ($reversedParts[0] === '[]') {
						$type = new Type\ArrayType(
							new Type\UnionType([new Type\IntegerType(), new Type\StringType()]),
							new Type\MixedType()
						);
					} else {
						$type = new Type\MixedType();
					}
					$reversedParts = array_slice($reversedParts, 2);
				} else {
					$type = $this->dbRowObjectType;
				}

				$last = NULL;
				foreach ($reversedParts as $part) {
					if ($part === '[]') {
						if (($last === '[]') || ($last === '|')) {
							return new Type\ErrorType(); // little hack, exception is thrown in fetchAssoc() - this should be nicer as PHPStan rule, but we need to care about this here too
						}

						$type =	Type\Accessory\AccessoryArrayListType::intersectWith(new Type\ArrayType(
							new Type\IntegerType(),
							$type
						));

						$last = $part;
					} else if ($part === '|') {
						if (($last === '[]') || ($last === '|')) {
							return new Type\ErrorType(); // little hack, exception is thrown in fetchAssoc() - this should be nicer as PHPStan rule, but we need to care about this here too
						}

						$type = new Type\ArrayType(
							new Type\UnionType([new Type\IntegerType(), new Type\StringType()]),
							$type
						);

						$last = $part;
					} else {
						if ($last !== '|') {
							$type = new Type\ArrayType(
								new Type\UnionType([new Type\IntegerType(), new Type\StringType()]),
								$type
							);
						}

						$last = NULL;
					}
				}

				return $type;
			}

			// this should cover PHPStan itself
			return new Type\ErrorType();
		} else if ($methodReflection->getName() === 'fetchPairs') {
			if (count($methodCall->getArgs()) === 1) {
				return new Type\ErrorType(); // little hack, exception is thrown in fetchPairs() - this should be nicer as PHPStan rule, but we need to care about this here too
			} else if (count($methodCall->getArgs()) === 2) {
				$keyArg = $methodCall->getArgs()[0]->value;
				$keyScopedType = $scope->getType($keyArg);

				$valueArg = $methodCall->getArgs()[1]->value;
				$valueScopedType = $scope->getType($valueArg);

				if ($keyScopedType->isNull()->no() && $valueScopedType->isNull()->yes()) {
					return new Type\ErrorType(); // little hack, exception is thrown in fetchPairs() - this should be nicer as PHPStan rule, but we need to care about this here too
				} else if ($keyScopedType->isNull()->yes() && $valueScopedType->isNull()->no()) {
					return Type\Accessory\AccessoryArrayListType::intersectWith(new Type\ArrayType(new Type\IntegerType(), new Type\MixedType()));
				}
			}

			return new Type\ArrayType(
				new Type\UnionType([new Type\IntegerType(), new Type\StringType()]),
				new Type\MixedType()
			);
		} else if ($methodReflection->getName() === 'getIterator') {
			return new Type\IterableType(new Type\IntegerType(), $this->dbRowObjectType);
		}

		// this should never happen
		throw new ShouldNotHappenException(\sprintf('Unsupported method \'%s\' in FetchDynamicMethodReturnTypeExtension.', $methodReflection->getName()));
	}

}
