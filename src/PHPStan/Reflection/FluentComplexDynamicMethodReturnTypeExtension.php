<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\PHPStan\Reflection;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PhpParser\Node\Expr\MethodCall;

final class FluentComplexDynamicMethodReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
	/** @var string */
	private $fluentQueryClass;


	public function __construct(string $fluentQueryClass)
	{
		$this->fluentQueryClass = $fluentQueryClass;
	}


	public function getClass(): string
	{
		return $this->fluentQueryClass;
	}


	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'query';
	}


	public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
	{
		return new ObjectType($this->getClass());
	}

}
