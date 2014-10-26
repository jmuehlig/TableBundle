<?php

namespace PZAD\TableBundle\Twig;

use PZAD\TableBundle\Table\Filter\FilterInterface;
use PZAD\TableBundle\Table\Renderer\RendererInterface;
use PZAD\TableBundle\Table\TableException;
use PZAD\TableBundle\Table\TableView;
use Twig_Extension;
use Twig_Function_Method;

class TableExtension extends Twig_Extension
{
	/**
	 * @var RendererInterface
	 */
	protected $tableRenderer;
	
	public function getName()
	{
		return 'table';
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
		return $this->getRenderer($tableView)->renderTableBegin($tableView);
	}
	
	public function getTableHeadContent(TableView $tableView)
	{
		return $this->getRenderer($tableView)->renderTableHead($tableView);
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
			return sprintf("%s\n %s\n %s %s\n %s",
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
		
		return implode("\n", $filterContent);
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
}

?>
