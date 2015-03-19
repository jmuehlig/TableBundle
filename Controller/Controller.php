<?php

namespace JGM\TableBundle\Controller;

use JGM\TableBundle\Table\Type\AbstractTableType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;
use JGM\TableBundle\Table\Table;

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
	 * @return	Table						Table.
	 */
	public function createTable(AbstractTableType $tableType)
	{
		return $this->get('jgm.table')->createTable($tableType);
	}
	
	/**
	 * Builds a table based on a anonymous builder function.
	 * 
	 * @param string		$entity	Name of the entity.
	 * @param callable		$build	Function for building the table.
	 * @param string|null	$name	Name of the table.
	 * 
	 * @return Table			Table.
	 */
	public function createAnonymousTable($entity, $build, $name = 'table')
	{
		return $this->get('jgm.table')->createAnonymousTable($entity, $build, $name);
	}
}
