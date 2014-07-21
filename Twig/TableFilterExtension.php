<?php

namespace PZAD\TableBundle\Twig;

use PZAD\TableBundle\Table\Filter\FilterRenderer;
use PZAD\TableBundle\Table\Filter\Filter;


class TableFilterExtension extends \Twig_Extension
{
	public function getName()
	{
		return 'table_filter';
	}
	
	public function getFunctions()
	{
		return array(
			'table_filter' => new \Twig_Function_Method($this, 'getTableFilterContent', array('is_safe' => array('html'))),
			
			'table_filter_start' => new \Twig_Function_Method($this, 'getTableFilterStartContent', array('is_safe' => array('html'))),
			'table_filter_end' => new \Twig_Function_Method($this, 'getTableFilterEndContent', array('is_safe' => array('html'))),
			'table_filter_button' => new \Twig_Function_Method($this, 'getTableFilterButtonContent', array('is_safe' => array('html'))),
			'table_filter_row' => new \Twig_Function_Method($this, 'getTableFilterRowContent', array('is_safe' => array('html'))),
			'table_filter_input'  => new \Twig_Function_Method($this, 'getTableFilterInputContent', array('is_safe' => array('html'))),
			'table_filter_label' => new \Twig_Function_Method($this, 'getTableFilterLabelContent', array('is_safe' => array('html')))
		);
	}
	
	public function getTableFilterContent(FilterRenderer $filterRenderer)
	{
		return $filterRenderer->render();
	}
	
	public function getTableFilterStartContent(FilterRenderer $filterRenderer)
	{
		return $filterRenderer->renderStart();
	}
	
	public function getTableFilterEndContent(FilterRenderer $filterRenderer)
	{
		return $filterRenderer->renderEnd();
	}
	
	public function getTableFilterButtonContent(FilterRenderer $filterRenderer)
	{
		$filterRenderer->renderButton();
	}
	
	public function getTableFilterRowContent(FilterRenderer $filterRenderer, Filter $filter)
	{
		return $filterRenderer->renderFilter($filter);
	}
	
	public function getTableFilterInputContent(FilterRenderer $filterRenderer, Filter $filter)
	{
		return $filterRenderer->renderFilterInput($filter);
	}
	
	public function getTableFilterLabelContent(FilterRenderer $filterRenderer, Filter $filter)
	{
		return $filterRenderer->renderFilterLabel($filter);
	}
}

?>
