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

	public function setName($name)
	{
		$this->name = $name;
	}

	public function setOptions(array $options)
	{
		$optionsResolver = new OptionsResolver();
		
		// Set defaults.
		$optionsResolver->setDefaults(array(
			'columns' => array(),
			'label' => '',
			'operator' => FilterOperator::EQ,
			'attr' => array()
		));
		
		// Set this filter default options.
		$this->setDefaultFilterOptions($optionsResolver);

		// Resolve options.
		$resolvedOptions = $optionsResolver->resolve($options);
		
		// Set intern properties from options.
		if(!is_array($resolvedOptions['columns']))
		{
			$this->columns = array($resolvedOptions['columns']);
		}
		else if(count($resolvedOptions['columns']) < 1)
		{
			$this->columns = array($this->getName());
		}
		else
		{
			$this->columns = $resolvedOptions['columns'];
		}

		$this->label = $resolvedOptions['label'];
		$this->operator = $resolvedOptions['operator'];
		$this->attributes = $resolvedOptions['attr'];
		
		FilterOperator::validate($this->operator);
		
		return $resolvedOptions;
	}
	
	/**
	 * Possibility for the filter to resolve his
	 * own options.
	 * 
	 * @param OptionsResolver $optionsResolver
	 */
	protected abstract function setDefaultFilterOptions(OptionsResolver $optionsResolver);
}
