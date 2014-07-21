<?php

namespace PZAD\TableBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use PZAD\TableBundle\Table\TableRenderer;
use PZAD\TableBundle\Table\TableView;
use PZAD\TableBundle\Table\Filter\FilterInterface;

class TableExtension extends \Twig_Extension
{
	protected $renderer;

	function __construct(ContainerInterface $container, RouterInterface $router)
	{
		$this->renderer = new TableRenderer($container, $container->get('request'), $router);
	}
	
	public function getName()
	{
		return 'table';
	}
	
	public function getFunctions()
	{
		return array(
			'table' => new \Twig_Function_Method($this, 'getTableContent', array('is_safe' => array('html'))),
			'table_begin' => new \Twig_Function_Method($this, 'getTableBeginContent', array('is_safe' => array('html'))),
			'table_head' => new \Twig_Function_Method($this, 'getTableHeadContent', array('is_safe' => array('html'))),
			'table_body' => new \Twig_Function_Method($this, 'getTableBodyContent', array('is_safe' => array('html'))),
			'table_end' => new \Twig_Function_Method($this, 'getTableEndContent', array('is_safe' => array('html'))),
			'table_pagination' => new \Twig_Function_Method($this, 'getTablePaginationContent', array('is_safe' => array('html'))),
			'table_filter' => new \Twig_Function_Method($this, 'getFilterContent', array('is_safe' => array('html'), 'needs_environment' => true))
		);
	}
	
	public function getTableContent(TableView $tableView)
	{
		return $this->renderer->render($tableView);
	}
	
	public function getTableBeginContent(TableView $tableView)
	{
		return $this->renderer->renderBegin($tableView);
	}
	
	public function getTableHeadContent(TableView $tableView)
	{
		return $this->renderer->renderHead($tableView);
	}
	
	public function getTableBodyContent(TableView $tableView)
	{
		return $this->renderer->renderBody($tableView);
	}
	
	public function getTableEndContent(TableView $tableView = null)
	{
		return $this->renderer->renderEnd();
	}
	
	public function getTablePaginationContent(TableView $tableView)
	{
		return $this->renderer->renderPagination($tableView);
	}
	
	public function getFilterContent(\Twig_Environment $twigEnviroment, $filters)
	{
		if($filters instanceof TableView)
		{
			$filters = $filters->getFilters();
		}
		
		if(is_array($filters))
		{
			$content = "";
			foreach($filters as $filter)
			{
				$content .= sprintf("%s <br />", $this->getSingleFilterContent($twigEnviroment, $filter));
			}
			
			return $content;
		}
		else
		{
			return $this->getSingleFilterContent($twigEnviroment, $filters);
		}
	}
	
	public function getSingleFilterContent(\Twig_Environment $twigEnviroment, FilterInterface $filter)
	{
		$filterClass = get_class($filter);
		$template = sprintf('PZADTableBundle:Filter:%s%s.html.twig', strtolower($filterClass{1}), substr($filterClass, 1));
		
		return $twigEnviroment->render($template, array(
			'filter' => $filter
		));
	}
}

?>
