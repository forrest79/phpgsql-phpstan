<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\PHPStan\PhpDoc;

use PHPStan\Analyser\NameScope;
use PHPStan\PhpDoc\TypeNodeResolverExtension;
use PHPStan\PhpDocParser\Ast;
use PHPStan\Type;

final class DbRowTypeNodeResolverExtension implements TypeNodeResolverExtension
{
	/** @var string */
	private $dbRowClass;


	public function __construct(string $dbRowClass)
	{
		$this->dbRowClass = $dbRowClass;
	}


	public function resolve(Ast\Type\TypeNode $typeNode, NameScope $nameScope): ?Type\Type
	{
		if (($typeNode instanceof Ast\Type\IdentifierTypeNode) && ($typeNode->name === 'DbRow')) {
			return new Type\ObjectType($this->dbRowClass);
		}

		return NULL;
	}

}
