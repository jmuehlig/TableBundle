<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table;

use JGM\TableBundle\Table\AccessValidation\AccessValidatorFactory;
use JGM\TableBundle\Table\Column\ColumnInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * The TableBuilder is concerned for the visualised columns.
 * Columns will added by the table type to the table builder.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class TableBuilder
{
	const OPTION_TEMPLATE = 'tempalte';
	const OPTION_EMPTY_VALUE = 'empty_value';
	const OPTION_ATTRIBUTES = 'attr';
	const OPTION_HEADER_ATTRIBUTES = 'header_attributes';
	const OPTION_HIDE_EMPTY_COLUMNS = 'hide_empty_columns';
	const OPTION_USE_FILTER = 'use_filter';
	const OPTION_USE_PAGINATION = 'use_pagination';
	const OPTION_USE_ORDER = 'use_order';
	const OPTION_USE_SELECTION = 'use_selection';
	const OPTION_LOAD_DATA = 'load_data';
	
	/**
	 * Container, will be distributed
	 * to columns, if they implemented
	 * a method called "setContainer".
	 * 
	 * @var ContainerInterface 
	 */
	private $container;
	
	/**
	 * @var AuthorizationCheckerInterface
	 */
	private $authorizationChecker;


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
	
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
		$this->authorizationChecker = $this->container->get('security.authorization_checker');
		
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
	 * @return TableBuilder
	 */
	public function add($type, $name, array $options = array())
	{
		if(array_key_exists($name, $this->columns))
		{
			TableException::duplicatedColumnName($this->container->get('jgm.table_context')->getCurrentTableName(), $name);
		}
		
		$type = strtolower($type);
		if(!array_key_exists($type, $this->registeredColumns))
		{
			TableException::columnTypeNotAllowed($this->container->get('jgm.table_context')->getCurrentTableName(), $type);
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
	 * Returns all columns as an array.
	 * 
	 * @return array
	 */
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
		$accessValidator = AccessValidatorFactory::getValidator($accessOption);
		if($accessValidator === null)
		{
			return false;
		}
		
		return $accessValidator->isAccessGranted($this->authorizationChecker);
	}
}
