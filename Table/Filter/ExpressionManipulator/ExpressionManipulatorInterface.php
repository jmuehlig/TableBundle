<?php
namespace JGM\TableBundle\Table\Filter\ExpressionManipulator;

/**
 * Interface for manipulators, which can manipulate
 * an expression for a column filter.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
interface ExpressionManipulatorInterface
{
	/**
	 * Name of the column, thats expression
	 * will be manipulated.
	 * 
	 * @return string
	 */
	public function getName();
	
	/**
	 * Returns the manipulated expression
	 * of the given columns name and value,
	 * if the value is known.
	 * 
	 * @return mixed
	 */
	public function getExpression($columnName, $columnValue = null);
}
