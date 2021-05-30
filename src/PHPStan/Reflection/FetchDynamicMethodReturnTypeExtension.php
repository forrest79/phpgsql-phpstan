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
		return \in_array($methodReflection->getName(), ['fetch', 'fetchAll', 'getIterator'], TRUE);
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
		} else if ($methodReflection->getName() === 'getIterator') {
			return new Type\IterableType(new Type\IntegerType(), new Type\ObjectType($this->dbRowClass));
		}

		throw new \RuntimeException(\sprintf('Unsupported method \'%s\'.', $methodReflection->getName()));
	}

}
