<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JGM\TableBundle\Table\Filter\ColumnExpression;

/**
 * Description of ColumnCountExpression
 *
 * @author Jan
 */
class ColumnCountExpression extends ColumnNameExpression
{
	public function __construct($columnName)
	{
		parent::__construct($columnName);
	}
}
