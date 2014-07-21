<?php

namespace PZAD\TableBundle\Table\Filter;

use Doctrine\ORM\EntityManager;
use PZAD\TableBundle\Table\Utils;

/**
 * Builder for building table filters.
 */
class FilterBuilder
{
	/**
	 * @var array
	 */
	private $_filters;
	
	private $_buttonOptions;
	
	/**
	 * @var EntityManager 
	 */
	private $_entityManager;
	
	public function __construct(EntityManager $entityManager)
	{
		$this->_filters = array();
		$this->_entityManager = $entityManager;
	}
	
	/**
	 * Adds a new filter for the table.
	 * 
	 * @param string $columnName	Name of the column which is filtered.
	 * @param int $operator			Operator.
	 * @param array $options		Options for the filter:
	 *									attr			CSS-Attributes for the input.
	 *									label			Label for the input.
	 *									placeholder		Placeholder for the input, if it is a text input.
	 *									values			Array or entity, if the column is an entity column.
	 */
	public function add($columnName, $operator, array $options = array())
	{
		$attributes = Utils::getOption('attr', $options);
		$placeholder = Utils::getOption('placeholder', $options);
		$label = Utils::getOption('label', $options, $columnName);
		$values = Utils::getOption('values', $options, array());
		
		$this->_filters[$columnName] = new Filter($columnName, $operator, $label, $placeholder, $values, $attributes);
		
		return $this;
	}
	
	public function setButton($name, array $options)
	{
		$attributes = Utils::getOption('attr', $options, array());
		$label = Utils::getOption('label', $options, $name);
		
		$this->_buttonOptions = array(
			'name' => $name,
			'label' => $label,
			'attributes' => $attributes
		);
		
		return $this;
	}
	
	public function getFilters()
	{
		return $this->_filters;
	}
	
	public function getButtonOptions()
	{
		return $this->_buttonOptions;
	}
}
