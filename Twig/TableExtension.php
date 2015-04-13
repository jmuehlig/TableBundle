<?php

namespace JGM\TableBundle\Twig;

use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\Model\SortableOptionsContainer;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Pagination\Strategy\StrategyFactory;
use JGM\TableBundle\Table\Pagination\Strategy\StrategyInterface;
use JGM\TableBundle\Table\Renderer\RendererInterface;
use JGM\TableBundle\Table\TableException;
use JGM\TableBundle\Table\TableView;
use JGM\TableBundle\Table\UrlHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Environment;
use Twig_Extension;
use Twig_Function_Method;
use Twig_Template;

/**
 * Twig extension for render the table view
 * at twig templates.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class TableExtension extends Twig_Extension
{
	/**
	 * @var RendererInterface
	 */
	protected $tableRenderer;
	
	/**
	 * @var Twig_Template
	 */
	protected $template;
	
	/**
	 * @var UrlHelper
	 */
	protected $urlHelper;
	
	/**
	 * Current rendererd table view.
	 * 
	 * @var TableView 
	 */
	protected $tableView;


	public function __construct(ContainerInterface $container)
	{
		$this->urlHelper = $container->get('jgm.url_helper');
	}
	
	public function getName()
	{
		return 'table';
	}
	
	public function initRuntime(Twig_Environment $environment) 
	{
		$this->template = $environment->loadTemplate('JGMTableBundle::blocks.html.twig');
	}
	
	private function getRenderer(TableView $view = null)
	{
		if($this->tableRenderer === null)
		{
			if($view === null)
			{
				return null;
			}
			
			$this->tableRenderer = $view->getTableRenderer();
		}
		
		return $this->tableRenderer;
	}
	
	public function getFunctions()
	{
		return array(
			// Table rendering.
			'table' => new Twig_Function_Method($this, 'getTableContent', array('is_safe' => array('html'))),
			'table_begin' => new Twig_Function_Method($this, 'getTableBeginContent', array('is_safe' => array('html'))),
			'table_head' => new Twig_Function_Method($this, 'getTableHeadContent', array('is_safe' => array('html'))),
			'table_body' => new Twig_Function_Method($this, 'getTableBodyContent', array('is_safe' => array('html'))),
			'table_end' => new Twig_Function_Method($this, 'getTableEndContent', array('is_safe' => array('html'))),
			'table_pagination' => new Twig_Function_Method($this, 'getTablePaginationContent', array('is_safe' => array('html'))),
			
			// Filter rendering.
			'filter' => new Twig_Function_Method($this, 'getFilterContent', array('is_safe' => array('html'))),
			'filter_label' => new Twig_Function_Method($this, 'getFilterLabelContent', array('is_safe' => array('html'))),
			'filter_widget' => new Twig_Function_Method($this, 'getFilterWidgetContent', array('is_safe' => array('html'))),
			'filter_row' => new Twig_Function_Method($this, 'getFilterRowContent', array('is_safe' => array('html'))),
			'filter_rows' => new Twig_Function_Method($this, 'getFilterRowsContent', array('is_safe' => array('html'))),
			'filter_begin' => new Twig_Function_Method($this, 'getFilterBeginContent', array('is_safe' => array('html'))),
			'filter_submit_button' => new Twig_Function_Method($this, 'getFilterSubmitButtonContent', array('is_safe' => array('html'))),
			'filter_reset_link' => new Twig_Function_Method($this, 'getFilterResetLinkContent', array('is_safe' => array('html'))),
			'filter_end' => new Twig_Function_Method($this, 'getFilterEndContent', array('is_safe' => array('html'))),
			
			// Some helper methods.
			'get_url_for_order' => new Twig_Function_Method($this, 'getUrlForOrder'),
			'get_url_for_page' => new Twig_Function_Method($this, 'getUrlForPage'),
			'get_url' => new Twig_Function_Method($this, 'getUrl'),
			
			'is_identical' => new Twig_Function_Method($this, 'isIdentical'),
		);
	}
	
	public function getTableContent(TableView $tableView)
	{
		$this->tableView = $tableView;
		
		return $this->template->renderBlock('table', array(
			'view' => $tableView,
		));
	}
	
	public function getTableBeginContent(TableView $tableView)
	{
		$this->tableView = $tableView;
		
		return $this->template->renderBlock('table_begin', array(
			'name' => $tableView->getName(),
			'attributes' => $tableView->getAttributes()
		));
	}
	
	public function getTableHeadContent(TableView $tableView)
	{
		$this->tableView = $tableView;
		
		// Create the route parameter names for each sortable column.
		$paramterNames = array();
		
		// Fill it with sortable parameter names.
		$sortable = $tableView->getOrder();
		if($sortable != null)
		{
			$paramterNames['column'] = $sortable->getParamColumnName();
			$paramterNames['direction'] = $sortable->getParamDirectionName();
		}
		
		// Fill it with the pagination parameter name.
		$pagination = $tableView->getPagination();
		if($pagination != null)
		{
			$paramterNames['page'] = $pagination->getParameterName();
		}
		
		return $this->template->renderBlock('table_head', array(
			'columns' => $tableView->getColumns(),
			'is_sortable' => $tableView->getOrder() != null,
			'parameterNames' => $paramterNames,
			'sort' => $sortable,
			'pagination' => $pagination
		));
	}
	
	public function getTableBodyContent(TableView $tableView)
	{
		$this->tableView = $tableView;
		
		return $this->template->renderBlock('table_body', array(
			'columns' => $tableView->getColumns(),
			'rows' => $tableView->getRows(),
			'emptyValue' => $tableView->getEmptyValue()
		));
	}
	
	public function getTableEndContent(TableView $tableView)
	{
		$this->tableView = $tableView;
		
		return $this->template->renderBlock('table_end', array());
	}
	
	public function getTablePaginationContent(TableView $tableView)
	{
		$this->tableView = $tableView;
		
		$pagination = $tableView->getPagination();
		
		if($pagination === null ||
			($tableView->getTotalPages() < 2 && $pagination->getShowEmpty() === false))
		{
			return;
		}
		
		// Get the page strategy.
		$strategy = StrategyFactory::getStrategy($tableView->getTotalPages(), $pagination->getMaxPages());
		/* @var $strategy StrategyInterface */ 

		return $this->template->renderBlock('table_pagination', array(
			'currentPage' => $pagination->getCurrentPage(),
			'prevLabel' => $pagination->getPreviousLabel(),
			'nextLabel' => $pagination->getNextLabel(),
			'totalPages' => $tableView->getTotalPages(),
			'classes' => $pagination->getClasses(),
			'pages' => $strategy->getPages($pagination->getCurrentPage(), $tableView->getTotalPages(), $pagination->getMaxPages())
		));
	}
	
	public function getFilterContent(TableView $tableView)
	{
		return $this->template->renderBlock('filter', array(
				'view' => $tableView
		));
	}
	
	public function getFilterBeginContent(TableView $tableView)
	{
		return $this->template->renderBlock('filter_begin', array(
			'needsFormEnviroment' => $this->getFilterNeedsFormEnviroment($tableView),
			'tableName' => $tableView->getName()
		));
	}
	
	public function getFilterWidgetContent(FilterInterface $filter)
	{
		return $this->template->renderBlock('filter_widget', array(
			'filter' => $filter
		));
	}
	
	public function getFilterLabelContent(FilterInterface $filter)
	{
		return $this->template->renderBlock('filter_label', array(
			'filter' => $filter
		));
	}
	
	public function getFilterRowContent(FilterInterface $filter)
	{
		return $this->template->renderBlock('filter_row', array(
			'filter' => $filter
		));
	}
	
	public function getFilterRowsContent($tableViewOrFilterArray)
	{
		$filters = array();
		if($tableViewOrFilterArray instanceof TableView)
		{
			$filters = $tableViewOrFilterArray->getFilters();
		}
		else if(is_array($tableViewOrFilterArray))
		{
			$filters = $tableViewOrFilterArray;
		}
		else
		{
			TableException::canNotRenderFilter();
		}
		
		return $this->template->renderBlock('filter_rows', array(
			'filters' => $filters
		));
	}
	
	public function getFilterSubmitButtonContent(TableView $tableView)
	{
		$filterOptions = $tableView->getFilter();
		if($filterOptions === null)
		{
			return;
		}
		
		return $this->template->renderBlock('filter_submit_button', array(
			'needsFormEnviroment' => $this->getFilterNeedsFormEnviroment($tableView),
			'submitLabel' => $filterOptions->getSubmitLabel(),
			'attributes' => $filterOptions->getSubmitAttributes()
		));
	}
	
	public function getFilterResetLinkContent(TableView $tableView)
	{
		$filterOptions = $tableView->getFilter();
		if($filterOptions === null)
		{
			return;
		}
		
		$filterParams = array();
		foreach($tableView->getFilters() as $filter)
		{
			$filterParams[$filter->getName()] = null;
		}
		
		return $this->template->renderBlock('filter_reset_link', array(
			'needsFormEnviroment' => $this->getFilterNeedsFormEnviroment($tableView),
			'resetLabel' => $filterOptions->getResetLabel(),
			'attributes' => $filterOptions->getResetAttributes(),
			'resetUrl' => $this->urlHelper->getUrlForParameters($filterParams)
		));
	}
	
	public function getFilterEndContent(TableView $tableView)
	{
		return $this->template->renderBlock('filter_end', array(
			'needsFormEnviroment' => $this->getFilterNeedsFormEnviroment($tableView)
		));
	}
	
	public function getUrl(array $params)
	{		
		return $this->urlHelper->getUrlForParameters($params);
	}
	
	public function getUrlForPage($page)
	{
		if($this->tableView === null)
		{
			TableException::tableViewNotSet();
		}
		
		if($this->tableView->getPagination() === null)
		{
			TableException::paginationNotProvided();
		}
		
		return $this->urlHelper->getUrlForParameters(array(
			$this->tableView->getPagination()->getParameterName() => $page
		));
	}
	
	public function getUrlForOrder($columnName)
	{
		if($this->tableView === null)
		{
			TableException::tableViewNotSet();
		}
		
		if($this->tableView->getOrder() === null)
		{
			TableException::orderNotProvided();
		}
		
		$order = $this->tableView->getOrder();
		
		$parameters = array($order->getParamColumnName() => $columnName);
		if($order->getCurrentColumnName() == $columnName && $order->getCurrentDirection() == Order::DIRECTION_ASC)
		{
			$parameters[$order->getParamDirectionName()] = Order::DIRECTION_DESC;
		}
		else
		{
			$parameters[$order->getParamDirectionName()] = Order::DIRECTION_ASC;
		}
		
		// Start at first page.
		if($this->tableView->getPagination() !== null)
		{
			$parameters[$this->tableView->getPagination()->getParameterName()] = 1;
		}
		
		return $this->urlHelper->getUrlForParameters($parameters);
	}
	
	protected function getFilterNeedsFormEnviroment(TableView $tableView)
	{
		$needsFormEnviroment = false;
		foreach($tableView->getFilters() as $filter)
		{
			/* @var $filter FilterInterface */
			if($filter->needsFormEnviroment())
			{
				$needsFormEnviroment = true;
				break;
			}
		}
		
		return $needsFormEnviroment;
	}
	
	public function isIdentical($obj1, $obj2)
	{
		return $obj1 === $obj2;
	}
}

?>
