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

use JGM\TableBundle\Table\DataSource\DataSourceInterface;
use JGM\TableBundle\Table\Filter\ExpressionManipulator\ExpressionManipulatorInterface;
use JGM\TableBundle\Table\Filter\ValueManipulator\ValueManipulatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * The AbstractFilter implements the base methods like setOptions
 * and getter of each option, given by the FilterInterface.
 *
 * @author 	Jan Mühlig <mail@janmuehlig.de>
 * @since 	1.0
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
	 * Expression name for each column.
	 * 
	 * @var array
	 */
	protected $columnExpressionNames;
	
	/**
	 * Expression for each column (not the name - the instance!).
	 * 
	 * @var array
	 */
	protected $columnExpressions;
	
	/**
	 * Attributes for rendering.
	 * 
	 * @var array 
	 */
	protected $attributes;
	
	/**
	 * Attributes for rendering the label.
	 * 
	 * @var array
	 */
	protected $labelAttributes;
	
	/**
	 * Value of this filter.
	 * 
	 * @var mixed
	 */
	protected $value = null;
	
	/**
	 * Default Value, if filter value is null.
	 * 
	 * @var mixed
	 */
	protected $defaultValue = null;
	
	/**
	 * Options of the filter.
	 * 
	 * @var array
	 */
	protected $options;
	
	/**
	 * @var ContainerInterface
	 */
	protected $containeInterface;
	
	/**
	 * Cache for all available filter expressions.
	 * 
	 * @var array
	 */
	protected $allFilterExpressions;
	
	/**
	 * Map of property names and option
	 * indexes.
	 * 
	 * @var array
	 */
	protected $optionPropertyMap = array();
	
	/**
	 * Value Manipulator.
	 * 
	 * @var ValueManipulatorInterface
	 */
	protected $valueManipulator;
	
	public function __construct(ContainerInterface $container)
	{
		$this->containeInterface = $container;
		$this->columnExpressions = array();
	}

	public function getAttributes()
	{
		return $this->attributes;
	}

	public function getColumns()
	{
		return $this->columns;
	}
	
	public function getExpressionForColumn(DataSourceInterface $dataSource, $columnName, $columnValue = null)
	{
		if(array_key_exists($columnName, $this->columnExpressionNames))
		{
			$expressionName = $this->columnExpressionNames[$columnName];
			
			if(!array_key_exists($columnName, $this->columnExpressions))
			{
				$filterExpressions = $this->getAllFilterExpressions();
				if(!array_key_exists($dataSource->getType(), $filterExpressions))
				{
					return $columnName;
				}

				foreach($filterExpressions[$dataSource->getType()] as $expressionClass)
				{
					$expressionInstance = new $expressionClass;
					/* @var $expressionInstance ExpressionManipulatorInterface */

					if($expressionInstance->getName() == $expressionName)
					{
						$this->columnExpressions[$columnName] = $expressionInstance;
					}
				}
			}
			
			$expressionManipulator = $this->columnExpressions[$columnName];
			/* @var $expressionManipulator ExpressionManipulatorInterface */
			
			return $expressionManipulator->getExpression($columnName, $columnValue);
		}
		
		return $columnName;
	}

	public function getLabel()
	{
		return $this->label;
	}
	
	public function getLabelAttributes()
	{
		return $this->labelAttributes;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getOperator()
	{
		return $this->operator;
	}
	
	/**
	 * 
	 * @return mixed	Value of this filter or default value,
	 *					if value is null and default value is not.
	 */
	public function getValue($mode = FilterInterface::FOR_FILTERING)
	{
		if($this->isActive() === false && $this->defaultValue !== null)
		{
			return $this->defaultValue;
		}
		
		if($this->valueManipulator !== null && $mode === FilterInterface::FOR_FILTERING)
		{
			return $this->valueManipulator->getValue($this->value);
		}
		
		return $this->value;
	}

	public function setName($name)
	{
		$this->name = $name;
	}
	
	public function setValue(array $value)
	{
		$this->value = $value[$this->getName()];
	}

	public function setOptions(array $options)
	{
		$optionsResolver = new OptionsResolver();

		// Set this filter default options.
		$this->setDefaultFilterOptions($optionsResolver);

		// Resolve options.
		$this->options = $optionsResolver->resolve($options);
		
		// Set intern properties from options.
		$columns = array();
		if(is_array($this->options['columns']))
		{
			$columns = $this->options['columns'];
		}
		else if(is_string($this->options['columns']))
		{
			$columns = array($this->options['columns']);
		}
		else
		{
			$columns = array($this->getName());
		}
		
		// Set expressions.
		$this->columnExpressionNames = array();
		foreach($columns as $key => $column)
		{
			if(strpos($column, '|') !== false)
			{
				// Split column in (0 => here.is.the.column.name) and (1 => expressionName).
				$columnParts = explode('|', $column);
				
				// Set the right column name without name of the expression,
				$columns[$key] = $columnParts[0];
				
				// Set expression.
				$this->columnExpressionNames[$columnParts[0]] = $columnParts[1];
			}
		}
		
		$this->columns = $columns;
		$this->label = $this->options['label'];
		$this->operator = $this->options['operator'];
		$this->attributes = $this->options['attr'];
		$this->labelAttributes = $this->options['label_attr'];
		$this->defaultValue = $this->options['default_value'];
		$this->valueManipulator = $this->options['value_manipulator'];
		FilterOperator::validate($this->operator);
	}
	
	/**
	 * Possibility for the filter to resolve his
	 * own options.
	 * 
	 * @param OptionsResolver $optionsResolver
	 */
	protected function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		$optionsResolver->setDefaults(array(
			'columns' => null,
			'label' => '',
			'operator' => FilterOperator::LIKE,
			'attr' => array(),
			'label_attr' => array('styles' => 'font-weight: bold'),
			'default_value' => null,
			'value_manipulator' => null
		));
	}
	
	public function __isset($name) 
	{
		$name = $this->getOptionIndexOfPropertyName($name);

		if(array_key_exists($name, $this->options))
		{
			return true;
		}
		
		return false;
	}
	
	public function __get($name)
	{
		$name = $this->getOptionIndexOfPropertyName($name);

		if(array_key_exists($name, $this->options))
		{
			return $this->options[$name];
		}
		
		return null;
	}
	
	/**
	 * For each getter, which is not implemented,
	 * there can be an option at the options array.
	 * This method will create an option name
	 * for a wanted getter method.
	 * 
	 * Example: Method 'getMyOption' will create
	 * index 'my_option'.
	 * 
	 * @param string $propertyName	Name of the wanted property.
	 * @return string				Index of the options array.
	 */
	protected function getOptionIndexOfPropertyName($propertyName)
	{
		if(!array_key_exists($propertyName, $this->optionPropertyMap))
		{
			// Replace CalmelCase to under_score: getMyOption => $options['my_option'].
			$this->optionPropertyMap[$propertyName] = preg_replace_callback(
				'/([A-Z])/',
				function($hit) {
					return sprintf("_%s", strtolower($hit[1]));
				},
				$propertyName
			);
		}
		
		return $this->optionPropertyMap[$propertyName];
	}
	
	public function getParameterNames()
	{
		return array($this->getName());
	}
	
	protected function getAllFilterExpressions()
	{
		if($this->allFilterExpressions === null)
		{
			$this->allFilterExpressions = $this->containeInterface->getParameter('jgm_table.filter_expressions');
		}
		
		return $this->allFilterExpressions;
	}
	
	public function isActive()
	{
		return $this->value !== null && !empty($this->value);
	}
}
