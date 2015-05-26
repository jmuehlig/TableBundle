<?php

namespace JGM\TableBundle\Table\Filter\ExpressionManipulator;

/**
 * Expression manipulator, which will
 * implement a max expression for the
 * QueryBuilderDataSource.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class DoctrineMaxExpressionManipulator implements ExpressionManipulatorInterface
{
	public function getExpression($columnName)
	{
		return sprintf("max(%s)", $columnName);
	}

	public function getName()
	{
		return 'max';
	}
}
