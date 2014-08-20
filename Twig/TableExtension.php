<?php

namespace PZAD\TableBundle\Twig;

use PZAD\TableBundle\Table\Filter\FilterRenderer;
use PZAD\TableBundle\Table\TableRenderer;
use PZAD\TableBundle\Table\TableView;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig_Environment;
use Twig_Extension;
use Twig_Function_Method;

class TableExtension extends Twig_Extension
{
	/**
	 * @var TableRenderer
	 */
	protected $tableRenderer;
	
	/**
	 * @var FilterRenderer
	 */
	protected $filterRenderer;
	
	function __construct(ContainerInterface $container, RouterInterface $router)
	{
		$this->tableRenderer = new TableRenderer($container, $container->get('request'), $router);
		$this->filterRenderer = new FilterRenderer($container);
	}
	
	public function getName()
	{
		return 'table';
	}
	
	public function getFunctions()
	{
		return array(
			'table' => new Twig_Function_Method($this, 'getTableContent', array('is_safe' => array('html'))),
			'table_begin' => new Twig_Function_Method($this, 'getTableBeginContent', array('is_safe' => array('html'))),
			'table_head' => new Twig_Function_Method($this, 'getTableHeadContent', array('is_safe' => array('html'))),
			'table_body' => new Twig_Function_Method($this, 'getTableBodyContent', array('is_safe' => array('html'))),
			'table_end' => new Twig_Function_Method($this, 'getTableEndContent', array('is_safe' => array('html'))),
			'table_pagination' => new Twig_Function_Method($this, 'getTablePaginationContent', array('is_safe' => array('html'))),
			'table_filter' => new Twig_Function_Method($this, 'getFilterContent', array('is_safe' => array('html')))
		);
	}
	
	public function getTableContent(TableView $tableView)
	{
		return $this->tableRenderer->render($tableView);
	}
	
	public function getTableBeginContent(TableView $tableView)
	{
		return $this->tableRenderer->renderBegin($tableView);
	}
	
	public function getTableHeadContent(TableView $tableView)
	{
		return $this->tableRenderer->renderHead($tableView);
	}
	
	public function getTableBodyContent(TableView $tableView)
	{
		return $this->tableRenderer->renderBody($tableView);
	}
	
	public function getTableEndContent(TableView $tableView = null)
	{
		return $this->tableRenderer->renderEnd();
	}
	
	public function getTablePaginationContent(TableView $tableView)
	{
		return $this->tableRenderer->renderPagination($tableView);
	}
	
	public function getFilterContent($filter)
	{
		return $this->filterRenderer->renderFilter($filter);
	}
}

?>
