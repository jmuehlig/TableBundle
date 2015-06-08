<?php

namespace JGM\TableBundle\Table;

use JGM\TableBundle\Table\Column\AccessValidation\CallableAccess;
use JGM\TableBundle\Table\Column\AccessValidation\ColumnAccessInterface;
use JGM\TableBundle\Table\Column\AccessValidation\RoleAccess;
use JGM\TableBundle\Table\Column\ColumnInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * The TableBuilder is concerned for the visualised columns.
 * Columns will added by the table type to the table builder.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
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
		$this->registeredColumns = $this->container->getParameter('jgm_table.columns');
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
	public function add($type, $name, array $options = array())
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
		
		// Check the columns access rights and delete option, if it exists.
		if(array_key_exists('access', $options))
		{
			$access = $options['access'];
			if($this->isAccessGranted($access) === false)
			{
				// User has no access to see this column.
				return $this;
			}
			
			unset($options['access']);
		}
		
		$column = new $this->registeredColumns[$type];
		/* @var $column ColumnInterface */
		
		$column->setName($name);
		$column->setOptions($options);
		
		if($column instanceof ContainerAwareInterface)
		{
			$column->setContainer($this->container);
		}
		
		$this->columns[$name] = $column;
		
		return $this;
	}
	
	/**
	 * Removes a column by its name.
	 * 
	 * @param string $columnName	Name of the column.
	 */
	public function removeColumn($columnName)
	{
		if(array_key_exists($columnName, $this->columns))
		{
			unset($this->columns[$columnName]);
		}
	}
	
	public function getColumns()
	{
		return $this->columns;
	}
	
	
	/**
	 * Checks whether the logged user has access to see this column.
	 * 
	 * @param	mixed	$accessOption
	 * 
	 * @return	bool	True, if access granted. False, otherwise.
	 */
	private function isAccessGranted($accessOption)
	{
		$securityContext = $this->container->get('security.context');
		/* @var $securityContext SecurityContextInterface */

		// If we found an array or string, it may be a role or a list of them.
		if(is_string($accessOption) || is_array($accessOption))
		{
			$accessOption = new RoleAccess($accessOption);
		}

		// If the option is callable, call them and check the result.
		else if(is_callable($accessOption))
		{
			$accessOption = new CallableAccess($accessOption);
		}

		// If the option is a column access interface, call it and check the result.
		if($accessOption instanceof ColumnAccessInterface)
		{
			try
			{
				if($accessOption->isAccessGranted($securityContext) !== true)
				{
					return false;
				}
			}
			catch(AccessDeniedException $ex)
			{
				return false;
			}
		}

		return true;
	}
}
