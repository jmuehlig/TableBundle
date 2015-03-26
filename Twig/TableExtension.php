<?php

namespace JGM\TableBundle\Twig;

use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\Model\SortableOptionsContainer;
use JGM\TableBundle\Table\Pagination\Model\Pagination;
use JGM\TableBundle\Table\Renderer\RendererInterface;
use JGM\TableBundle\Table\TableException;
use JGM\TableBundle\Table\TableView;
use JGM\TableBundle\Table\UrlHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Environment;
use Twig_Extension;
use Twig_Function_Method;
use Twig_Template;

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
			// Table rendering
			'table' => new Twig_Function_Method($this, 'getTableContent', array('is_safe' => array('html'))),
			'table_begin' => new Twig_Function_Method($this, 'getTableBeginContent', array('is_safe' => array('html'))),
			'table_head' => new Twig_Function_Method($this, 'getTableHeadContent', array('is_safe' => array('html'))),
			'table_body' => new Twig_Function_Method($this, 'getTableBodyContent', array('is_safe' => array('html'))),
			'table_end' => new Twig_Function_Method($this, 'getTableEndContent', array('is_safe' => array('html'))),
			'table_pagination' => new Twig_Function_Method($this, 'getTablePaginationContent', array('is_safe' => array('html'))),
			
			// Filter rendering
			'filter' => new Twig_Function_Method($this, 'getFilterContent', array('is_safe' => array('html'))),
			'filter_label' => new Twig_Function_Method($this, 'getFilterLabelContent', array('is_safe' => array('html'))),
			'filter_widget' => new Twig_Function_Method($this, 'getFilterWidgetContent', array('is_safe' => array('html'))),
			'filter_begin' => new Twig_Function_Method($this, 'getFilterBeginContent', array('is_safe' => array('html'))),
			'filter_submit_button' => new Twig_Function_Method($this, 'getFilterSubmitButtonContent', array('is_safe' => array('html'))),
			'filter_reset_link' => new Twig_Function_Method($this, 'getFilterResetLinkContent', array('is_safe' => array('html'))),
			'filter_end' => new Twig_Function_Method($this, 'getFilterEndContent', array('is_safe' => array('html'))),
			
			'get_table_url' => new Twig_Function_Method($this, 'getTableUrl'),
		);
	}
	
	public function getTableContent(TableView $tableView)
	{
		return sprintf("%s\n %s\n %s\n %s\n %s",
			$this->getTableBeginContent($tableView),
			$this->getTableHeadContent($tableView),
			$this->getTableBodyContent($tableView),
			$this->getTableEndContent($tableView),
			$this->getTablePaginationContent($tableView)				
		);
	}
	
	public function getTableBeginContent(TableView $tableView)
	{
		return $this->template->renderBlock('table_begin', array(
			'name' => $tableView->getName(),
			'attributes' => $tableView->getAttributes()
		));
	}
	
	public function getTableHeadContent(TableView $tableView)
	{			
		// Create the route parameter names for each sortable column.
		$paramterNames = array();
		
		// Fill it with sortable parameter names.
		$sortable = $tableView->getSortable();
		if($sortable != null)
		{
			$paramterNames['column'] = $sortable->getParamColumnName();
			$paramterNames['direction'] = $sortable->getDirection();
		}
		
		// Fill it with the pagination parameter name.
		$pagination = $tableView->getPagination();
		if($pagination != null)
		{
			$paramterNames['page'] = $pagination->getParameterName();
		}
		
		return $this->template->renderBlock('table_head', array(
			'columns' => $tableView->getColumns(),
			'is_sortable' => $tableView->getSortable() != null,
			'parameterNames' => $paramterNames,
			'sort' => $sortable,
			'pagination' => $pagination
		));
	}
	
	public function getTableBodyContent(TableView $tableView)
	{
		return $this->getRenderer($tableView)->renderTableBody($tableView);
	}
	
	public function getTableEndContent(TableView $tableView)
	{
		return $this->getRenderer($tableView)->renderTableEnd($tableView);
	}
	
	public function getTablePaginationContent(TableView $tableView)
	{
		return $this->getRenderer($tableView)->renderTablePagination($tableView);
	}
	
	public function getFilterContent($viewOrFilterOrArray)
	{
		if($viewOrFilterOrArray instanceof TableView)
		{
			return sprintf("%s\n %s <br /> %s %s\n %s",
				$this->getFilterBeginContent($viewOrFilterOrArray),
				$this->getFilterArrayContent($viewOrFilterOrArray->getFilters()),
				$this->getFilterSubmitButtonContent($viewOrFilterOrArray),
				$this->getFilterResetLinkContent($viewOrFilterOrArray),
				$this->getFilterEndContent($viewOrFilterOrArray)				
			);
		}
		else if($viewOrFilterOrArray instanceof FilterInterface)
		{
			return $this->getFilterSingleContent($viewOrFilterOrArray);
		}
		else if(is_array($viewOrFilterOrArray))
		{
			return $this->getFilterArrayContent($viewOrFilterOrArray);
		}
		else
		{
			TableException::canNotRenderFilter();
		}
	}
	
	public function getFilterBeginContent(TableView $tableView)
	{
		return $this->getRenderer($tableView)->renderFilterBegin($tableView);
	}
	
	public function getFilterSubmitButtonContent(TableView $tableView)
	{
		return $this->getRenderer($tableView)->renderFilterSubmitButton($tableView);
	}
	
	public function getFilterResetLinkContent(TableView $tableView)
	{
		return $this->getRenderer($tableView)->renderFilterResetLink($tableView);
	}
	
	public function getFilterEndContent(TableView $tableView)
	{
		return $this->getRenderer($tableView)->renderFilterEnd($tableView);
	}
	
	private function getFilterArrayContent(array $filters)
	{
		$filterContent = array();
		foreach($filters as $filter)
		{
			/* @var $filter FilterInterface */
			$filterContent[] = $this->getFilterSingleContent($filter);
		}
		
		return implode("<br />", $filterContent);
	}
			
	private function getFilterSingleContent(FilterInterface $filter)
	{
		return sprintf("%s\n%s", $this->getFilterLabelContent($filter), $this->getFilterWidgetContent($filter));
	}
	
	public function getFilterWidgetContent(FilterInterface $filter)
	{
		if($this->getRenderer() === null)
		{
			TableException::filterNoView();
		}
		
		return $this->getRenderer()->renderFilter($filter);
	}
	
	public function getFilterLabelContent(FilterInterface $filter)
	{
		return $this->getRenderer()->renderFilterLabel($filter);
	}
	
	public function getTableUrl($pagination, $sort, $page, $columnName, $direction = null)
	{
		return $this->urlHelper->getUrl($pagination, $sort, $page, $columnName, $direction);
	}
}

?>
