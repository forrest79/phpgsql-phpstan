<?php declare(strict_types=1);

namespace Forrest79\PhPgSql\PHPStan;

use PHPStan\Type;

final class Helper
{

	/**
	 * @param array<Type\Constant\ConstantStringType> $constantStrings
	 */
	public static function getImplodedConstantString(array $constantStrings): string
	{
		return implode('|', array_map(static function (Type\Constant\ConstantStringType $constantStringType): string {
			return $constantStringType->getValue();
		}, $constantStrings));
	}

}
