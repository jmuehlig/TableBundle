<?php

namespace PZAD\TableBundle\Table\Filter;

use PZAD\TableBundle\Table\TableException;

/**
 * Available filter operators.
 */
class FilterOperator
{
	const EQ		= 0;
	const LIKE		= 1;
	const LT		= 2;
	const GT		= 3;
	const LEQ		= 4;
	const GEQ		= 5;
	const NOT_EQ	= 6;
	const NOT_LIKE	= 7;
	
	/**
	 * Ensures that the $operator is a valid filter operator.
	 * 
	 * @param int $operator
	 */
	public static function validate($operator)
	{
		$validOperators = array(self::EQ, self::LIKE, self::LT, self::GT, self::LEQ, self::GEQ, self::NOT_EQ, self::NOT_LIKE);
		if(!in_array($operator, $validOperators))
		{
			TableException::operatorNotValid($operator, $validOperators);
		}
	}
}
