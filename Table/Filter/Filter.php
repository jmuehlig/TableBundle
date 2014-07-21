<?php

namespace PZAD\TableBundle\Table\Filter;

/**
 * Represents a filter.
 */
class Filter
{
	/**
	 * @var string
	 */
	private $_columnName;
	
	/**
	 * @var int
	 */
	private $_operator;
	
	/**
	 * @var string 
	 */
	private $_label;
	
	/**
	 * @var array
	 */
	private $_values;
	
	/**
	 * @var string 
	 */
	private $_placeholder;
	
	/**
	 * @var array 
	 */
	private $_attributes;
	
	/**
	 * Represents a filter for a table.
	 * 
	 * @param string $columnName	Name of the column, which is filtered by this filter.
	 * @param int $operator			Type of the operator.
	 * @param string $label			Label for the input field.
	 * @param string $placeholder	Placeholder, if it is a text input.
	 * @param array $values			Available values, represented in a select input.
	 * @param array $attributes		Attributes for the input field.
	 */
	public function __construct($columnName, $operator, $label, $placeholder, $values, $attributes)
	{
		$this->_columnName = $columnName;
		$this->_operator = $operator;
		$this->_label = $label;
		$this->_placeholder = $placeholder;
		$this->_values = $values;
		$this->_attributes = $attributes;
	}
	
	public function getColumnName()
	{
		return $this->_columnName;
	}

	public function getOperator()
	{
		return $this->_operator;
	}

	public function getLabel()
	{
		return $this->_label;
	}

	public function getValues()
	{
		return $this->_values;
	}

	public function getPlaceholder()
	{
		return $this->_placeholder;
	}

	public function getAttributes()
	{
		return $this->_attributes;
	}
	
	public function setValues(array $values)
	{
		$this->_values = $values;
	}
}
