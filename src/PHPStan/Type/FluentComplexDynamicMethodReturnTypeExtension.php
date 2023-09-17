<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\PHPStan\Type;

use Forrest79\PhPgSql;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type;
use PhpParser\Node\Expr\MethodCall;

final class FluentComplexDynamicMethodReturnTypeExtension implements Type\DynamicMethodReturnTypeExtension
{
	private string $fluentQueryClass;


	public function __construct(string $fluentQueryClass)
	{
		$this->fluentQueryClass = $fluentQueryClass;
	}


	public function getClass(): string
	{
		return PhPgSql\Fluent\Complex::class;
	}


	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'query';
	}


	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope,
	): Type\Type
	{
		return new Type\ObjectType($this->fluentQueryClass);
	}

}
