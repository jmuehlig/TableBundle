<?php

namespace PZAD\TableBundle\Table;

use Symfony\Component\DependencyInjection\ContainerInterface;
use PZAD\TableBundle\Table\Column\ColumnInterface;

/**
 * The TableBuilder is concerned for the
 * visualised columns.
 * Columns will added by the table type to the table builder.
 *
 * @author Jan Mühlig
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
	private $addedColumns;
	
	/**
	 * Registered column classes.
	 * 
	 * @var array 
	 */
	private $registeredColumns;
	
	function __construct(ContainerInterface $container)
	{
		$this->container = $container;
		
		$this->addedColumns = array();
		
		// Register standard columns.
		$this->registeredColumns = array();
		$this->registerColumnType(Column\ColumnType::CONTENT, 'PZAD\TableBundle\Table\Column\ContentColumn');
		$this->registerColumnType(Column\ColumnType::ENTITY, 'PZAD\TableBundle\Table\Column\EntityColumn');
		$this->registerColumnType(Column\ColumnType::DATE, 'PZAD\TableBundle\Table\Column\DateColumn');
		$this->registerColumnType(Column\ColumnType::TEXT, 'PZAD\TableBundle\Table\Column\TextColumn');
		$this->registerColumnType(Column\ColumnType::NUMBER, 'PZAD\TableBundle\Table\Column\NumberColumn');
		$this->registerColumnType(Column\ColumnType::COUNTER, 'PZAD\TableBundle\Table\Column\CounterColumn');
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
		if(array_key_exists($name, $this->addedColumns))
		{
			TableException::duplicatedColumnName($name);
		}
		
		$type = strtolower($type);
		if(!array_key_exists($type, $this->registeredColumns))
		{
			TableException::typeNotAllowed($type);
		}
		
		$column = new $this->registeredColumns[$type];
		/* @var $column ColumnInterface */
		
		$column->setName($name);
		$column->setOptions($options);
		
		if(is_callable(array($column, 'setContainer')))
		{
			$column->setContainer($this->container);
		}
		
		$this->addedColumns[$name] = $column;
		
		return $this;
	}
	
	/**
	 * Registeres a new type for columns. 
	 * 
	 * @param string $type		Type of the column.
	 * @param string $class		Class for the column type (with namespace).
	 * 
	 * @return TableBuilder
	 */
	public function registerColumnType($type, $class)
	{
		if(array_key_exists($type, $this->registeredColumns))
		{
			TableException::columnTypeAlreadyRegistered($type);
		}
		
		if(!class_exists($class))
		{
			TableException::columnClassNotFound($class);
		}
		
		$columnType = new $class;
		if(!$columnType instanceof Column\ColumnInterface)
		{
			TableException::columnClassNotImplementingInterface($class);
		}
		
		$this->registeredColumns[$type] = $class;
		
		return $this;
	}
	
	public function getColumns()
	{
		return $this->addedColumns;
	}
}
