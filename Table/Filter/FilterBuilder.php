<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Filter;

use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\TableException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builder for building table filters.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class FilterBuilder
{
	/**
	 * @var array
	 */
	private $filters;
	
	/**
	 * @var array
	 */
	private $registeredFilters;
	
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	public function __construct(ContainerInterface $container)
	{
		$this->filters = array();
		
		$this->registeredFilters = $container->getParameter('jgm_table.filters');
		
		$this->container = $container;
	}
	
	public function add($type, $name, array $options = array())
	{
		if(array_key_exists($name, $this->filters))
		{
			TableException::duplicatedFilterName($this->container->get('jgm.table_context')->getCurrentTableName(), $name);
		}
		
		$filterClass = $this->getFilterClass($type);
		$filter = new $filterClass($this->container);
		/* @var $filter FilterInterface */
		
		if(!$filter instanceof FilterInterface)
		{
			TableException::filterClassNotImplementingInterface(
				$this->container->get('jgm.table_context')->getCurrentTableName(), 
				$filter
			);
		}
		
		$filter->setName($name);
		$filter->setOptions($options);
		
		$this->filters[$name] = $filter;
		
		return $this;
	}
	
	public function getFilters()
	{
		return $this->filters;
	}
	
	private function getFilterClass($type)
	{
		if(class_exists($type) && is_subclass_of($type, FilterInterface::class))
		{
			return $type;
		}
		else if(array_key_exists(strtolower($type), $this->registeredFilters))
		{
			@trigger_error(
				'Using an alias for column type is deprecated since v1.4 and will be removed at v1.6. Use class name like "TextColumn::class" for naming column types.',
				E_USER_DEPRECATED
			);
			return $this->registeredFilters[strtolower($type)];
		}
			
		TableException::filterTypeNotAllowed($this->container->get('jgm.table_context')->getCurrentTableName(), $type, array_keys($this->registeredFilters));
	}
}
