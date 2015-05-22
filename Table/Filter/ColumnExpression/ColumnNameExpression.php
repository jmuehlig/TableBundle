<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JGM\TableBundle\Table\Filter\ColumnExpression;

/**
 * Description of ColumnNameExpression
 *
 * @author Jan
 */
class ColumnNameExpression implements ColumnExpressionInterface
{
	protected $columnName;
	
	public function __construct($columnName)
	{
		$this->columnName = $columnName;
	}

	public function getColumnName()
	{
		return $this->columnName;
	}

}
