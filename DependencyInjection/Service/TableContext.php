<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\DependencyInjection\Service;

use JGM\TableBundle\Table\Table;

/**
 * The TableContext holds tables while creating them
 * or their views.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.2
 */
class TableContext
{
	/**
	 * Stack of active tables.
	 * 
	 * @var array
	 */
	protected $tableStack;
	
	/**
	 * List of all registered tables.
	 * 
	 * @var array 
	 */
	protected $registeredTables;
	
	public function __construct()
	{
		$this->tableStack = array();
		$this->registeredTables = array();
	}
	
	/**
	 * Registers the given table.
	 * 
	 * @param Table $table
	 */
	public function registerTable(Table $table)
	{
		$this->tableStack[] = $table;
		$this->registeredTables[$table->getName()] = $table;
	}
	
	/**
	 * Unregisters the given table.
	 * 
	 * @param Table $table
	 */
	public function unregisterTable(Table $table)
	{
		if(($key = array_search($table, $this->tableStack)) !== false) 
		{
			unset($this->tableStack[$key]);
		}
	}
	
	/**
	 * Returns the active handled table, or null,
	 * if no table is handled.
	 * A table is handled while creating the table 
	 * or the table view.
	 * 
	 * @return	Table
	 */
	public function getCurrentTable()
	{
		$count = count($this->tableStack);
		if($count > 1)
		{
			return $this->tableStack[$count-1];
		}
		
		return null;
	}
	
	public function getCurrentTableName()
	{
		$table = $this->getCurrentTable();
		if($table === null)
		{
			return null;
		}
		
		return $table->getName();
	}
	
	/**
	 * Returns True, if any table is handled.
	 * 
	 * @return boolean
	 */
	public function hasTable()
	{
		return count($this->tableStack) > 0;
	}
	
	/**
	 * Returns a list of all registered tables,
	 * also those tables, who áre unregistered.
	 * 
	 * @return array
	 */
	public function getAllRegisteredTables()
	{
		return $this->registeredTables;
	}
}
