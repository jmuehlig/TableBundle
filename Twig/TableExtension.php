<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Twig;

use JGM\TableBundle\DependencyInjection\Service\TableStopwatchService;
use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Pagination\Strategy\StrategyFactory;
use JGM\TableBundle\Table\Pagination\Strategy\StrategyInterface;
use JGM\TableBundle\Table\Renderer\RendererInterface;
use JGM\TableBundle\Table\TableException;
use JGM\TableBundle\Table\TableView;
use JGM\TableBundle\Table\Utils\UrlHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;
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
	 * @var TableStopwatchService
	 */
	protected $stopwatchService;
	
	/**
	 * Current rendererd table view.
	 * 
	 * @var TableView 
	 */
	protected $tableView;


	public function __construct(ContainerInterface $container, TableStopwatchService $stopwatchService)
	{
		$this->urlHelper = $container->get('jgm.url_helper');
		$this->stopwatchService = $stopwatchService;
	}
	
	public function getName()
	{
		return 'table';
	}
	
	protected function init(TableView $tableView, Twig_Environment $environment) 
	{
		$this->tableView = $tableView;
		
		if($this->template === null || $this->template->getTemplateName() !== $tableView->getTemplateName())
		{
			$this->template = $environment->loadTemplate($tableView->getTemplateName());
		}
	}
	
	public function getFunctions()
	{
		return array(
			// Table rendering.
			new Twig_SimpleFunction ('table', array($this, 'getTableContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('table_begin', array($this, 'getTableBeginContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('table_head', array($this, 'getTableHeadContent'), array('is_safe' => array('html'))),
			new Twig_SimpleFunction ('table_body', array($this, 'getTableBodyContent'), array('is_safe' => array('html'))),
			new Twig_SimpleFunction ('table_end', array($this, 'getTableEndContent'), array('is_safe' => array('html'))),
			new Twig_SimpleFunction ('table_pagination', array($this, 'getTablePaginationContent'), array('is_safe' => array('html'))),
			
			// Filter rendering.
			new Twig_SimpleFunction ('filter', array($this, 'getFilterContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('filter_label', array($this, 'getFilterLabelContent'), array('is_safe' => array('html'))),
			new Twig_SimpleFunction ('filter_widget', array($this, 'getFilterWidgetContent'), array('is_safe' => array('html'))),
			new Twig_SimpleFunction ('filter_row', array($this, 'getFilterRowContent'), array('is_safe' => array('html'))),
			new Twig_SimpleFunction ('filter_rows', array($this, 'getFilterRowsContent'), array('is_safe' => array('html'))),
			new Twig_SimpleFunction ('filter_begin', array($this, 'getFilterBeginContent'), array('is_safe' => array('html'), 'needs_environment' => true)),
			new Twig_SimpleFunction ('filter_submit_button', array($this, 'getFilterSubmitButtonContent'), array('is_safe' => array('html'))),
			new Twig_SimpleFunction ('filter_reset_link', array($this, 'getFilterResetLinkContent'), array('is_safe' => array('html'))),
			new Twig_SimpleFunction ('filter_end', array($this, 'getFilterEndContent'), array('is_safe' => array('html'))),
			
			// Some helper methods.
			new Twig_SimpleFunction ('get_url_for_order', array($this, 'getUrlForOrder')),
			new Twig_SimpleFunction ('get_url_for_page', array($this, 'getUrlForPage')),
			new Twig_SimpleFunction ('get_url', array($this, 'getUrl'))
		);
	}
	
	public function getTableContent(Twig_Environment $environment, TableView $tableView)
	{
		$this->init($tableView, $environment);
		
		return $this->template->renderBlock('table', array(
			'view' => $tableView,
		));
	}
	
	public function getTableBeginContent(Twig_Environment $environment, TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		$this->init($tableView, $environment);

		$content = $this->template->renderBlock('table_begin', array(
			'name' => $tableView->getName(),
			'attributes' => $tableView->getAttributes()
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		return $content;
	}
	
	public function getTableHeadContent(TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		$this->tableView = $tableView;
		
		// Create the route parameter names for each sortable column.
		$paramterNames = array();
		
		// Fill it with sortable parameter names.
		$sortable = $tableView->getOrder();
		if($sortable !== null)
		{
			$paramterNames['column'] = $sortable->getParamColumnName();
			$paramterNames['direction'] = $sortable->getParamDirectionName();
		}
		
		// Fill it with the pagination parameter name.
		$pagination = $tableView->getPagination();
		if($pagination !== null)
		{
			$paramterNames['page'] = $pagination->getParameterName();
		}
		
		$content = $this->template->renderBlock('table_head', array(
			'columns' => $tableView->getColumns(),
			'is_sortable' => $tableView->getOrder() !== null,
			'parameterNames' => $paramterNames,
			'sort' => $sortable,
			'pagination' => $pagination
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		return $content;
	}
	
	public function getTableBodyContent(TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		$this->tableView = $tableView;
		
		$content = $this->template->renderBlock('table_body', array(
			'columns' => $tableView->getColumns(),
			'rows' => $tableView->getRows(),
			'emptyValue' => $tableView->getEmptyValue()
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		return $content;
	}
	
	public function getTableEndContent(TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		$this->tableView = $tableView;
		
		$content = $this->template->renderBlock('table_end', array());
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		return $content;
	}
	
	public function getTablePaginationContent(TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
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

		$content = $this->template->renderBlock('table_pagination', array(
			'currentPage' => $pagination->getCurrentPage(),
			'prevLabel' => $pagination->getPreviousLabel(),
			'nextLabel' => $pagination->getNextLabel(),
			'totalPages' => $tableView->getTotalPages(),
			'classes' => $pagination->getClasses(),
			'pages' => $strategy->getPages($pagination->getCurrentPage(), $tableView->getTotalPages(), $pagination->getMaxPages())
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_TABLE);
		
		return $content;
	}
	
	public function getFilterContent(Twig_Environment $environment, TableView $tableView)
	{
		$this->init($tableView, $environment);
		
		return $this->template->renderBlock('filter', array(
				'view' => $tableView
		));
	}
	
	public function getFilterBeginContent(Twig_Environment $environment, TableView $tableView)
	{
		$this->init($tableView, $environment);
		
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		$content = $this->template->renderBlock('filter_begin', array(
			'needsFormEnviroment' => $this->getFilterNeedsFormEnviroment($tableView),
			'tableName' => $tableView->getName()
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		return $content;
	}
	
	public function getFilterWidgetContent(FilterInterface $filter)
	{
		$this->stopwatchService->start($this->tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		$content = $this->template->renderBlock('filter_widget', array(
			'filter' => $filter
		));
		
		$this->stopwatchService->stop($this->tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		return $content;
	}
	
	public function getFilterLabelContent(FilterInterface $filter)
	{
		$this->stopwatchService->start($this->tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		$content = $this->template->renderBlock('filter_label', array(
			'filter' => $filter
		));
		
		$this->stopwatchService->stop($this->tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		return $content;
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
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		$filterOptions = $tableView->getFilter();
		if($filterOptions === null)
		{
			return;
		}
		
		$content = $this->template->renderBlock('filter_submit_button', array(
			'needsFormEnviroment' => $this->getFilterNeedsFormEnviroment($tableView),
			'submitLabel' => $filterOptions->getSubmitLabel(),
			'attributes' => $filterOptions->getSubmitAttributes()
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		return $content;
	}
	
	public function getFilterResetLinkContent(TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
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
		
		$content = $this->template->renderBlock('filter_reset_link', array(
			'needsFormEnviroment' => $this->getFilterNeedsFormEnviroment($tableView),
			'resetLabel' => $filterOptions->getResetLabel(),
			'attributes' => $filterOptions->getResetAttributes(),
			'resetUrl' => $this->urlHelper->getUrlForParameters($filterParams)
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		return $content;
	}
	
	public function getFilterEndContent(TableView $tableView)
	{
		$this->stopwatchService->start($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		$content = $this->template->renderBlock('filter_end', array(
			'needsFormEnviroment' => $this->getFilterNeedsFormEnviroment($tableView)
		));
		
		$this->stopwatchService->stop($tableView->getName(), TableStopwatchService::CATEGORY_RENDER_FILTER);
		
		return $content;
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
		foreach($tableView->getFilters() as $filter)
		{
			/* @var $filter FilterInterface */
			if($filter->needsFormEnviroment())
			{
				return true;
			}
		}
		
		return false;
	}
}
