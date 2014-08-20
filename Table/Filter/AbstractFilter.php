<?php

namespace PZAD\TableBundle\Table\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The AbstractFilter implements the base methods like setOptions
 * and getter of each option, given by the FilterInterface.
 * The method for rendering must be implemented by each specific
 * filter itself.
 *
 * @author 	Jan Mühlig <mail@janmuehlig.de>
 * @since 	1.0.0
 */
abstract class AbstractFilter implements FilterInterface
{
	/**
	 * Name of the filter.
	 * 
	 * @var string
	 */
	protected $name;
	
	/**
	 * Label of the filter.
	 * 
	 * @var string
	 */
	protected $label;
	
	/**
	 * Operator of the filter.
	 * 
	 * @var int
	 */
	protected $operator;
	
	/**
	 * Columns, the filter will work on.
	 * 
	 * @var array
	 */
	protected $columns;
	
	/**
	 * Attributes for rendering.
	 * 
	 * @var array 
	 */
	protected $attributes;
	
	/**
	 * All not clear resolved options.
	 * 
	 * @var array
	 */
	protected $furtherOptions;

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function getColumns()
	{
		return $this->columns;
	}

	public function getLabel()
	{
		return $this->label;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getOperator()
	{
		return $this->operator;
	}
	
	public function getFurtherOptions()
	{
		return $this->getFurtherOptions();
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function setOptions(array $options)
	{
		$optionsResolver = new OptionsResolver();
		
		// Set required.
		$optionsResolver->setRequired(array(
			'columns'
		));
		
		// Set defaults.
		$optionsResolver->setDefaults(array(
			'label' => '',
			'operator' => FilterOperator::EQ,
			'attr' => array()
		));
		
		// Resolve options.
		$resolvedOptions = $optionsResolver->resolve($options);
		
		$this->columns = is_array($resolvedOptions['columns']) ? $resolvedOptions['columns'] : array($resolvedOptions['columns']);
		$this->label = $resolvedOptions['label'];
		$this->operator = $resolvedOptions['operator'];
		$this->attributes = $resolvedOptions['attr'];
		
		$this->furtherOptions = array();
		foreach($resolvedOptions as $key => $option)
		{
			if(!in_array($key, array('columns','label','operator','attr')))
			{
				$this->furtherOptions[$key] = $option;
			}
		}
		
		FilterOperator::validate($this->operator);
	}
}
