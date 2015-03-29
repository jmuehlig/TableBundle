<?php

namespace JGM\TableBundle\Table\Renderer;

use JGM\TableBundle\Table\Column\ColumnInterface;
use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\Pagination\Strategy\StrategyFactory;
use JGM\TableBundle\Table\Pagination\Strategy\StrategyInterface;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Table\TableView;
use JGM\TableBundle\Table\Utils\UrlHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Default renderer for a table.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0.0
 */
class DefaultRenderer implements RendererInterface
{	
	/**
	 * Container.
	 * 
	 * @var ContainerInterface
	 */
	protected $container;
	
	/**
	 * Request.
	 * 
	 * @var Request 
	 */
	protected $request;
	
	/**
	 * Router.
	 * 
	 * @var RouterInterface
	 */
	protected $router;
	
	/**
	 * URL Generator.
	 * 
	 * @var UrlHelper
	 */
	protected $urlHelper;

	function __construct(ContainerInterface $container, Request $request, RouterInterface $router)
	{
		$this->container	= $container;
		$this->request		= $request;
		$this->router		= $router;
		$this->urlHelper	= $container->get('jgm.url_helper');
	}
	
	/**
	 * Render the table begin (<table> tag)
	 * 
	 * @param $tableView TableView	View of the table.
	 * 
	 * @return string HTML Code.
	 */
	public function renderTableBegin(TableView $tableView)
	{

	}
	
	/**
	 * Render the table head.
	 * 
	 * @param $tableView TableView	View of the table.	
	 * 
	 * @return string HTML Code.
	 */
	public function renderTableHead(TableView $tableView)
	{		

	}
	
	/**
	 * Render the table body.
	 * 
	 * @param $tableView TableView	View of the table.
	 * 
	 * @return string HTML Code.
	 */
	public function renderTableBody(TableView $tableView)
	{
	}
	
	/**
	 * Render the table end (</table>).
	 * 
	 * @param $tableView TableView	View of the table.
	 * 
	 * @return string HTML Code.
	 */
	public function renderTableEnd(TableView $tableView = null)
	{
	}
	
	/**
	 * Render the pagination.
	 * 
	 * @param $tableView TableView	View of the table.
	 * 
	 * @return string HTML Code.
	 */
	public function renderTablePagination(TableView $tableView)
	{
	}
	
	/**
	 * Reneres the header of a column with the sort-arrow-class,
	 * if the table is sortable and the column is the sortet column.
	 * 
	 * @param $tableView TableView	View of the table.
	 * @param $column	 Column		Column to be rendered.
	 * @return string				HTML Code
	 */
	private function renderSortableColumnHeader(TableView $tableView, ColumnInterface $column)
	{
	}

	public function renderFilter(FilterInterface $filter)
	{
		return $filter->render();
	}
	
	public function renderFilterLabel(FilterInterface $filter)
	{
		return $filter->renderLabel();
	}

	public function renderFilterBegin(TableView $tableView)
	{
		foreach($tableView->getFilters() as $filter)
		{
			/* @var $filter FilterInterface */
			if($filter->needsFormEnviroment() === true)
			{
				return "<form>";
			}
		}
	}

	public function renderFilterEnd(TableView $tableView)
	{
		foreach($tableView->getFilters() as $filter)
		{
			/* @var $filter FilterInterface */
			if($filter->needsFormEnviroment())
			{
				return "</form>";
			}
		}
	}

	public function renderFilterResetLink(TableView $tableView)
	{
		foreach($tableView->getFilters() as $filter)
		{
			/* @var $filter FilterInterface */
			if($filter->needsFormEnviroment() === true)
			{
				$filterParams = array();
				foreach($tableView->getFilters() as $filter)
				{
					$filterParams[$filter->getName()] = null;
				}

				return sprintf(
					"<a href=\"%s\"%s>%s</a>",
					$this->urlHelper->getUrlForParameters($filterParams, $tableView->getName()),
					RenderHelper::attrToString($tableView->getFilter()->getResetAttributes()),
					$tableView->getFilter()->getResetLabel()
				);
			}
		}
	}

	public function renderFilterSubmitButton(TableView $tableView)
	{
		foreach($tableView->getFilters() as $filter)
		{
			/* @var $filter FilterInterface */
			if($filter->needsFormEnviroment() === true)
			{
				return sprintf(
					"<input type=\"submit\" value=\"%s\" %s />",
					$tableView->getFilter()->getSubmitLabel(),
					RenderHelper::attrToString($tableView->getFilter()->getSubmitAttributes())
				);
			}
		}
	}
}