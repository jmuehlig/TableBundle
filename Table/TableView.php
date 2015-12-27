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

use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\Filter\Model\Filter;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Pagination\Model\Pagination;

/**
 * TablieView
 * 
 * Represents a table in the view layer.
 * Used by twig to create
 * the HTML based output for the table.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class TableView
{
	/**
	 * Name of the tabe.
	 * 
	 * @var string 
	 */
	protected $name;
	
	/**
	 * Rows of the table, represented as array
	 * of Row-Objects.
	 * 
	 * @var array 
	 */
	protected $rows;
	
	/**
	 * Columns of the table, represented as
	 * array of ColumnInterface-Objects.
	 * 
	 * @var array 
	 */
	protected $columns;
	
	/**
	 * Filters if the table, represented
	 * as array of Filter-Objects.
	 * Only if the table type implementing
	 * the FilterInterface.
	 * 
	 * @var array 
	 */
	protected $filters;
	
	/**
	 * Options for the table pagination.
	 * Only, if pagination is defined by
	 * the table type.
	 * 
	 * @var Pagination
	 */
	protected $pagination;
	
	/**
	 * Options for the table filters.
	 * Only, if filter are defined by
	 * the table type.
	 * 
	 * @var Filter 
	 */
	protected $filter;
	
	/**
	 * Options for the table sorting.
	 * Only, if sortable is defined by
	 * the table type.
	 * 
	 * @var Order 
	 */
	protected $order;
	
	/**
	 * The message, displayed if the rows-array
	 * is empty.
	 * 
	 * @var string
	 */
	protected $emptyValue;	
	
	/**
	 * Attributes for the table.
	 * 
	 * @var array 
	 */
	protected $attributes;
	
	/**
	 * Attributes for the table head.
	 * 
	 * @var array 
	 */
	protected $headAttributes;
	
	/**
	 * Attributes for the rows, filled by
	 * the getRowAttributes-Method of the
	 * table type.
	 * 
	 * @var array 
	 */
	protected $rowAttributes;
	
	/**
	 * Number of total pages, 1 if pagination
	 * is not available.
	 * 
	 * @var int
	 */
	protected $totalPages;
	
	/**
	 * Number of total items.
	 * 
	 * @var int
	 */
	protected $totalItems;
	
	public function __construct($name, array $columns, array $rows,
		array $filters, $pagination, $order, $filter, $emptyValue, array $attributes,
		array $headAttributes, $totalPages, $totalItems
	)
	{
		// Set up the class vars.
		$this->name				= $name;
		$this->columns			= $columns;
		$this->rows				= $rows;
		$this->filters			= $filters;
		$this->pagination		= $pagination;
		$this->order			= $order;
		$this->filter			= $filter;
		$this->emptyValue		= $emptyValue;
		$this->attributes		= $attributes;
		$this->headAttributes	= $headAttributes;
		$this->totalPages		= $totalPages;
		$this->totalItems		= $totalItems;
	}
	
	// Begin of getters for the class vars.
	public function getName()
	{
		return $this->name;
	}
	
	public function getRows()
	{
		return $this->rows;
	}

	public function getColumns()
	{
		return $this->columns;
	}
	
	public function getFilters()
	{
		return $this->filters;
	}

	public function getPagination()
	{
		return $this->pagination;
	}

	public function getOrder()
	{
		return $this->order;
	}
	
	public function getFilter()
	{
		return $this->filter;
	}
	
	/**
	 * Returns an array of active filters.
	 * 
	 * @return array
	 */
	public function getActiveFilter()
	{
		$filter = array();
		
		foreach($this->filter as $filter)
		{
			/* @var $filter FilterInterface */
			
			if($filter->isActive() === true)
			{
				$filter[] = $filter;
			}
		}
		
		return $filter;
	}

	public function getEmptyValue()
	{
		return $this->emptyValue;
	}

	public function getAttributes()
	{
		return $this->attributes;
	}
	
	public function getHeadAttributes()
	{
		return $this->headAttributes;
	}
	
	public function getTotalPages()
	{
		return $this->totalPages;
	}

	public function getTotalItems()
	{
		return $this->totalItems;
	}
}
