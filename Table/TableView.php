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
use JGM\TableBundle\Table\OptionsResolver\TableOptions;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Pagination\Model\Pagination;
use JGM\TableBundle\Table\Pagination\OptionsResolver\PaginationOptions;
use JGM\TableBundle\Table\Row\Row;

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
	 * Filters represented
	 * as array of Filter-Objects.
	 * Only if the table type implementing
	 * the FilterInterface.
	 * 
	 * @var array 
	 */
	protected $filters;

	/**
	 * List of buttons for selection.
	 * 
	 * @var array
	 */
	protected $selectionButtons;
	
	/**
	 * List including all options.
	 * 
	 * @var array
	 */
	protected $options;
	
	/**
	 * Callable for row attributes.
	 * 
	 * @var callable
	 */
	protected $rowAttributeCallback;

	public function __construct($name, array $options, array $columns, array $rows,	array $filters, array $selectionButtons, $rowAttributeCallback)
	{
		// Set up the class vars.
		$this->name				= $name;
		$this->options			= $options;
		$this->columns			= $columns;
		$this->rows				= $rows;
		$this->filters			= $filters;
		$this->selectionButtons = $selectionButtons;
		$this->rowAttributeCallback = $rowAttributeCallback;
	}
	
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
	
	public function getSelectionButtons()
	{
		return $this->selectionButtons;
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
	
	public function hasPagination()
	{
		return array_key_exists('pagination', $this->options);
	}
	
	public function hasFilter()
	{
		return array_key_exists('filter', $this->options);
	}
	
	public function hasOrder()
	{
		return array_key_exists('order', $this->options);
	}
	
	public function getTableOption($optionName)
	{
		return $this->getOptions('table', $optionName);
	}
	
	public function getPaginationOption($optionName)
	{
		return $this->getOptions('pagination', $optionName);
	}
	
	public function getOrderOption($optionName)
	{
		return $this->getOptions('order', $optionName);
	}
	
	public function getFilterOption($optionName)
	{
		return $this->getOptions('filter', $optionName);
	}
	
	public function getRowAttributes(Row $row)
	{
		return call_user_func($this->rowAttributeCallback, $row);
	}
	
	private function getOptions($module, $optionName)
	{
		return $this->options[$module][$optionName];
	}
	
	/**
	 * @deprecated since version 1.3
	 */
	public function getPagination()
	{
		if($this->hasPagination() === false)
		{
			return null;
		}
		
		return new Pagination($this->options['pagination']);
	}

	/**
	 * @deprecated since version 1.3
	 */
	public function getOrder()
	{
		if($this->hasOrder() === false)
		{
			return null;
		}
		
		return new Order($this->options['order']);
	}
	
	/**
	 * @deprecated since version 1.3
	 */
	public function getFilter()
	{
		if($this->hasFilter() === false)
		{
			return null;
		}
		
		return new Filter($this->options['filter']);
	}
	
	/**
	 * @deprecated since version 1.3
	 */
	public function getEmptyValue()
	{
		return $this->getTableOption(TableOptions::EMPTY_VALUE);
	}

	/**
	 * @deprecated since version 1.3
	 */
	public function getAttributes()
	{
		return $this->getTableOption(TableOptions::ATTRIBUTES);
	}
	
	/**
	 * @deprecated since version 1.3
	 */
	public function getHeadAttributes()
	{
		return $this->getTableOption(TableOptions::HEAD_ATTRIBUTES);
	}
	
	/**
	 * @deprecated since version 1.3
	 */
	public function getTotalPages()
	{
		return $this->getPaginationOption(PaginationOptions::TOTAL_PAGES);
	}

	/**
	 * @deprecated since version 1.3
	 */
	public function getTotalItems()
	{
		return $this->getTableOption(TableOptions::TOTAL_ITEMS);
	}
	
	/**
	 * @deprecated since version 1.3
	 */
	public function getTemplateName()
	{
		return $this->getTableOption(TableOptions::TEMPLATE);
	}
}
