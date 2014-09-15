<?php

namespace PZAD\TableBundle\Table;

use Symfony\Component\DependencyInjection\ContainerInterface;
use PZAD\TableBundle\Table\Column\ColumnInterface;

/**
 * The TableBuilder is concerned for the visualised columns.
 * Columns will added by the table type to the table builder.
 *
 * @author Jan Mühlig <mail@janmuehlig.de>
 * @since 1.0.0
 */
class TableBuilder
{
	/**
	 * Container, will be distributed
	 * to columns, if they implemented
	 * a method called "setContainer".
	 * 
	 * @var ContainerInterface 
	 */
	private $container;


	/**
	 * Array of all added columns.
	 * 
	 * @var array 
	 */
	private $columns;
	
	/**
	 * Registered column classes.
	 * 
	 * @var array 
	 */
	private $registeredColumns;
	
	function __construct(ContainerInterface $container)
	{
		$this->container = $container;
		
		$this->columns = array();
		
		// Register standard columns.
		$this->registeredColumns = $this->container->getParameter('pzad_table.columns');
	}
	
	/**
	 * Adds a new column to the table.
	 * 
	 * @param string $type		Type of the column.
	 * @param string $name		Name of the column.
	 * @param array $options	Array with options for the column.
	 * 
	 * @return TableBuilder		TableBuilder for add more columns, set options and so on.
	 */
	public function add($type, $name, array $options)
	{
		if(array_key_exists($name, $this->columns))
		{
			TableException::duplicatedColumnName($name);
		}
		
		$type = strtolower($type);
		if(!array_key_exists($type, $this->registeredColumns))
		{
			TableException::columnTypeNotAllowed($type);
		}
		
		$column = new $this->registeredColumns[$type];
		/* @var $column ColumnInterface */
		
		$column->setName($name);
		$column->setOptions($options);
		
		if(is_callable(array($column, 'setContainer')))
		{
			$column->setContainer($this->container);
		}
		
		$this->columns[$name] = $column;
		
		return $this;
	}
	
	public function getColumns()
	{
		return $this->columns;
	}
}
