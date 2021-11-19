<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\PHPStan\Reflection;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type;
use PhpParser\Node\Expr\MethodCall;

abstract class FetchDynamicMethodReturnTypeExtension implements Type\DynamicMethodReturnTypeExtension
{
	/** @var string */
	private $dbRowClass;


	public function __construct(string $dbRowClass)
	{
		$this->dbRowClass = $dbRowClass;
	}


	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return \in_array($methodReflection->getName(), ['fetch', 'fetchAll', 'fetchAssoc', 'getIterator'], TRUE);
	}


	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type\Type
	{
		if ($methodReflection->getName() === 'fetch') {
			return new Type\UnionType([new Type\ObjectType($this->dbRowClass), new Type\NullType()]);
		} else if ($methodReflection->getName() === 'fetchAll') {
			return new Type\ArrayType(new Type\IntegerType(), new Type\ObjectType($this->dbRowClass));
		} else if ($methodReflection->getName() === 'fetchAssoc') {
			if (count($methodCall->getArgs()) === 1) {
				$arg = $methodCall->getArgs()[0]->value;
				$scopedType = $scope->getType($arg);

				// this should cover PHPStan itself
				if (!($scopedType instanceof Type\Constant\ConstantStringType)) {
					throw new \InvalidArgumentException('fetchAssoc() descriptor is not string.');
				}

				$assocDesc = $scopedType->getValue();

				$parts = \preg_split('#(\[\]|=|\|)#', $assocDesc, -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);
				if (($parts === FALSE) || ($parts === [])) {
					self::fetchAssocBadDescriptor($assocDesc); // this should be nicer as PHPStan rule, but we need to care about here too
				}

				$firstPart = \reset($parts);
				$lastPart = \end($parts);
				if (($firstPart === '=') || ($firstPart === '|') || ($lastPart === '=') || ($lastPart === '|')) {
					self::fetchAssocBadDescriptor($assocDesc); // this should be nicer as PHPStan rule, but we need to care about here too
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
					$type = new Type\ObjectType($this->dbRowClass);
				}

				$last = NULL;
				foreach ($reversedParts as $part) {
					if ($part === '[]') {
						if (($last === '[]') || ($last === '|')) {
							self::fetchAssocBadDescriptor($assocDesc); // this should be nicer as PHPStan rule, but we need to care about here too
						}

						$type =	new Type\ArrayType(
							new Type\IntegerType(),
							$type
						);

						$last = $part;
					} else if ($part === '|') {
						if (($last === '[]') || ($last === '|')) {
							self::fetchAssocBadDescriptor($assocDesc); // this should be nicer as PHPStan rule, but we need to care about here too
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
			throw new \InvalidArgumentException('Only one argument is allowed in fetchAssoc().');
		} else if ($methodReflection->getName() === 'getIterator') {
			return new Type\IterableType(new Type\IntegerType(), new Type\ObjectType($this->dbRowClass));
		}

		// this should never happen
		throw new \InvalidArgumentException(\sprintf('Unsupported method \'%s\' in FetchDynamicMethodReturnTypeExtension.', $methodReflection->getName()));
	}


	/**
	 * @phpstan-return never
	 */
	private static function fetchAssocBadDescriptor(string $assocDesc): void
	{
		throw new \InvalidArgumentException(sprintf('Bad descriptor in fetchAssoc(\'%s\').', $assocDesc));
	}

}
