<?php

namespace PZAD\TableBundle\Table;

use PZAD\TableBundle\Table\Renderer\RendererInterface;

/**
 * TablieView
 * 
 * Represents a table in the view layer.
 * Used by the TableRenderer to create
 * the HTML based output for the table.
 *
 * @author Jan Mühlig
 * @since 1.0
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
	 * Renderer of the table.
	 * @var RendererInterface
	 */
	protected $tableRenderer;
	
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
	 * @var array
	 */
	protected $pagination;
	
	/**
	 * Options for the table sorting.
	 * Only, if sortable is defined by
	 * the table type.
	 * 
	 * @var array 
	 */
	protected $sortable;
	
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
	
	public function __construct($name, RendererInterface $renderer, array $columns, array $rows, array $filters, array $pagination, array $sortable, $emptyValue, array $attributes, array $headAttributes)
	{
		// Set up the class vars.
		$this->name				= $name;
		$this->tableRenderer	= $renderer;
		$this->columns			= $columns;
		$this->rows				= $rows;
		$this->filters			= $filters;
		$this->pagination		= $pagination;
		$this->sortable			= $sortable;
		$this->emptyValue		= $emptyValue;
		$this->attributes		= $attributes;
		$this->headAttributes	= $headAttributes;
	}
	
	// Begin of getters for the class vars.
	public function getName()
	{
		return $this->name;
	}
	
	public function getTableRenderer()
	{
		return $this->tableRenderer;
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

	public function getSortable()
	{
		return $this->sortable;
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
}
