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
	
	public function add($type, $name, $options)
	{
		
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
