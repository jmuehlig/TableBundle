<?php

namespace PZAD\TableBundle\Table\Filter;

use PZAD\TableBundle\Table\Table;
use PZAD\TableBundle\Table\Utils;
use PZAD\TableBundle\Table\Column\ColumnType;

/**
 * Filter renderer.
 */
class FilterRenderer
{
	/**
	 * Table, which filters will be rendered.
	 * 
	 * @var Table
	 */
	private $_table;
	
	public function __construct(Table $table)
	{
		$this->_table = $table;
	}
	
	public function render()
	{
		$content = $this->renderStart();
		
		$filters = $this->_table->getFilters();
		foreach($filters as $key => $filter)
		{
			/* @var $filter Filter */
			$content .= sprintf("%s<br />", $this->renderFilter($filter));
		}
		
		return $content . $this->renderButton() . $this->renderEnd();
	}
	
	public function renderStart()
	{
		return sprintf("<form method=\"GET\" name=\"%s\">", $this->_table->getName());
	}
	
	public function renderEnd()
	{
		return "</form>";
	}
	
	public function renderFilter(Filter $filter)
	{
		return sprintf('<span>%s: </span><span>%s</span>', $this->renderFilterLabel($filter), $this->renderFilterInput($filter));
	}
	
	public function renderFilterInput(Filter $filter)
	{
		$value = $this->_table->getRequest()->get($filter->getColumnName(), null);
				
		if($filter->getValues() !== null && count($filter->getValues()) > 0)
		{
			$content = sprintf('<select name="%s"%s>', $filter->getColumnName(), Utils::renderAttributesContent($filter->getAttributes()));
			foreach($filter->getValues() as $key => $value)
			{
				$selected = $value == $key ? ' selected="selected"' : '';
				$content .= sprintf('<option value="%s"%s>%s</option>', $key, $selected, $value);
			}
			$content .= '</select>';
			return $content;
		}
		else
		{
			return sprintf(
				'<input type="text" name="%s" value="%s" placeholder="%s"%s />',
				$filter->getColumnName(),
				$value,
				$filter->getPlaceholder(),
				Utils::renderAttributesContent($filter->getAttributes())
			);
		}
	}
	
	public function renderFilterLabel(Filter $filter)
	{
		return $filter->getLabel();
	}
	
	public function renderButton()
	{
		$options = $this->_table->getFilterButtonOptions();
		$name = Utils::getOption('name', $options, 'submitFilter');
		$attributes = Utils::getOption('attributes', $options, array());
		$label = Utils::getOption('label', $options, $name);
		
		return sprintf('<input type="submit" value="%s"%s />', $name, $label, Utils::renderAttributesContent($attributes));
	}
	
	public function __get($filterName)
	{
		$filters = $this->_table->getFilters();
		if(!array_key_exists($filters, $filterName))
		{
			FilterException::FilterNotFound($filterName);
		}
		
		return $filters[$filterName];
	}
}
