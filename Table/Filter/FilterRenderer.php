<?php

namespace PZAD\TableBundle\Table\Filter;

use PZAD\TableBundle\Table\TableView;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Filter renderer.
 */
class FilterRenderer
{
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}
	
	/**
	 * Renders the given filter(s).
	 * 
	 * @param array|FilterInterface|TableView
	 */
	public function renderFilter($filter)
	{
		$content = "<form>";
		
		if($filter instanceof FilterInterface)
		{
			$content .= $this->renderSingleFilter($filter);
		}
		else if($filter instanceof TableView)
		{
			$content .= $this->renderSetOfFilters($filter->getFilters());
		}
		else if(is_array($filter))
		{
			$content .= $this->renderSetOfFilters($filter);
		}
		else
		{
			FilterException::isNoValidFilter(get_class($filter));
		}
		
		$content .= "</form>";
		
		return $content;
	}
	
	private function renderSingleFilter(FilterInterface $filter)
	{
		return $filter->render($this->container);
	}
	
	private function renderSetOfFilters(array $filters)
	{
		$content = "";
		foreach($filters as $filter)
		{
			$content .= sprintf("%s <br />", $this->renderSingleFilter($filter));
		}
		
		return $content;
	}
}
