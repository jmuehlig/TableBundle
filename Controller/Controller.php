<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Controller;

use JGM\TableBundle\Table\AnonymousTableBuilder;
use JGM\TableBundle\Table\Table;
use JGM\TableBundle\Table\Type\AbstractTableType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;

/**
 * Extending the Symfony Controller with methods
 * for creating tables.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class Controller extends SymfonyController
{
	/**
	 * Builds a table by a table type.
	 * 
	 * @param AbstractTableType $tableType	TableType.
	 * @return	Table
	 */
	public function createTable(AbstractTableType $tableType, array $options = array())
	{
		return $this->get('jgm.table')->createTable($tableType, $options);
	}
	
	/**
	 * Creats a table builder, which is used to create
	 * tables without implementing a table type.
	 * 
	 * @param string $name	Name of the table.
	 * @param array $options	Options of the table.
	 * 
	 * @return AnonymousTableBuilder
	 */
	public function getTableBuilder($name, array $options = array())
	{
		return $this->get('jgm.table')->getTableBuilder($name, $options);
	}
}
