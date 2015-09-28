<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Filter;

use JGM\TableBundle\Table\TableException;

/**
 * Available filter operators.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
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
