<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\PHPStan\Analyser;

use DG;
use PHPStan\Analyser;
use PHPStan\Reflection;
use PHPStan\Type;
use PhpParser\Node;
use Tester;

final class IsDbRowFunctionTypeSpecifyingExtension implements Type\FunctionTypeSpecifyingExtension, Analyser\TypeSpecifierAwareExtension
{
	/** @var bool */
	private static $bypassFinals = FALSE;

	/** @var string */
	private $dbRowClass;

	/** @var Analyser\TypeSpecifier */
	private $typeSpecifier;


	public function __construct(string $dbRowClass)
	{
		$this->dbRowClass = $dbRowClass;
	}


	public function isFunctionSupported(
		Reflection\FunctionReflection $functionReflection,
		Node\Expr\FuncCall $node,
		Analyser\TypeSpecifierContext $context
	): bool
	{
		return ($functionReflection->getName() === 'is_dbrow') && isset($node->getArgs()[0]);
	}


	public function specifyTypes(
		Reflection\FunctionReflection $functionReflection,
		Node\Expr\FuncCall $node,
		Analyser\Scope $scope,
		Analyser\TypeSpecifierContext $context
	): Analyser\SpecifiedTypes
	{
		$args = $node->getArgs();
		$expr = $args[0]->value;

		$dbRowClass = $this->dbRowClass;
		if (isset($args[1])) {
			$withPropertiesArg = $args[1]->value;
			$scopedType = $scope->getType($withPropertiesArg);

			if ($scopedType instanceof Type\Constant\ConstantArrayType) {
				$columns = [];
				foreach ($scopedType->getKeyTypes() as $key) {
					if ($key instanceof Type\Constant\ConstantStringType) {
						$columns[] = $key->getValue();
					} else {
						break;
					}
				}

				// there are some columns and all is string type
				if (($columns !== []) && (count($columns) === count($scopedType->getKeyTypes()))) {
					$types = [];
					foreach ($scopedType->getValueTypes() as $value) {
						if ($value instanceof Type\Constant\ConstantStringType) {
							$types[] = $value->getValue();
						} else {
							break;
						}
					}

					if (count($columns) === count($types)) {
						$rowHash = sha1(serialize(array_combine($columns, $types)));
						$dbRowClass = sprintf('%s_%s', str_replace('\\', '_', $dbRowClass), $rowHash);

						if (!class_exists($dbRowClass)) {
							$dbRowProperties = '';
							foreach ($columns as $i => $column) {
								$dbRowProperties .= sprintf('/** @var %s */public $%s;', $types[$i], $column);
							}

							self::bypassFinals();
							eval(sprintf('class %s extends %s {%s}', $dbRowClass, $this->dbRowClass, $dbRowProperties));
						}
					}
				}
			}
		}

		$type = new Type\ObjectType($dbRowClass);

		return $this->typeSpecifier->create($expr, $type, Analyser\TypeSpecifierContext::createTruthy());
	}


	public function setTypeSpecifier(Analyser\TypeSpecifier $typeSpecifier): void
	{
		$this->typeSpecifier = $typeSpecifier;
	}


	private static function bypassFinals(): void
	{
		if (!self::$bypassFinals) {
			if (class_exists(DG\BypassFinals::class)) {
				DG\BypassFinals::enable();
			} else if (class_exists(Tester\Environment::class)) {
				Tester\Environment::bypassFinals();
			}

			self::$bypassFinals = TRUE;
		}
	}

}