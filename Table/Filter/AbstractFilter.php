<?php

namespace JGM\TableBundle\Table\Filter;

use JGM\TableBundle\Table\Renderer\RenderHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
	 * Map of property names and option
	 * indexes.
	 * 
	 * @var array
	 */
	protected $optionPropertyMap = array();
	
	public function __construct(ContainerInterface $container)
	{
		$this->containeInterface = $container;
	}

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
	
	public function getValue()
	{
		if($this->value === null && $this->defaultValue !== null)
		{
			return $this->defaultValue;
		}

		return $this->value;
	}

	public function setName($name)
	{
		$this->name = $name;
	}
	
	public function setValue($value)
	{
		$this->value = $value;
	}

	public function setOptions(array $options)
	{
		$optionsResolver = new OptionsResolver();

		// Set this filter default options.
		$this->setDefaultFilterOptions($optionsResolver);

		// Resolve options.
		$this->options = $optionsResolver->resolve($options);
		
		// Set intern properties from options.
		if(!is_array($this->options['columns']))
		{
			$this->columns = array($this->options['columns']);
		}
		else if(count($this->options['columns']) < 1)
		{
			$this->columns = array($this->getName());
		}
		else
		{
			$this->columns = $this->options['columns'];
		}

		$this->label = $this->options['label'];
		$this->operator = $this->options['operator'];
		$this->attributes = $this->options['attr'];
		$this->labelAttributes = $this->options['label_attr'];
		$this->defaultValue = $this->options['default_value'];
		
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
			'columns' => array(),
			'label' => '',
			'operator' => FilterOperator::LIKE,
			'attr' => array(),
			'label_attr' => array('styles' => 'font-weight: bold'),
			'default_value' => null
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
	
	public function renderLabel()
	{
		if($this->label != '')
		{
			return sprintf(
				"<label for=\"%s\"%s>%s</label>",
				$this->getName(),
				RenderHelper::attrToString($this->labelAttributes),
				$this->label
			);
		}
	}
	
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
}
