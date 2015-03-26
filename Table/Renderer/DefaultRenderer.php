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
		return sprintf(
			"<a name=\"%s\"></a>\n
			<table id=\"%s\"%s>",
			$tableView->getName(),
			$tableView->getName(),
			RenderHelper::attrToString($tableView->getAttributes())
		);
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
		$content = "<thead>";
		$content .= sprintf("<tr%s>", RenderHelper::attrToString($tableView->getHeadAttributes()));
		
		foreach($tableView->getColumns() as $column)
		{
			/* @var $column ColumnInterface */

			// Render table column head with attributes for the head column
			// and a link for sortable columns.
			$content .= sprintf(
				"<th%s>%s</th>",
				RenderHelper::attrToString($column->getHeadAttributes()),
				$this->renderSortableColumnHeader($tableView, $column)
			);
		}
		
		$content .= "</tr>";
		$content .= "</thead>";
		
		return $content;
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
		$content = "<tbody>";
		
		foreach($tableView->getRows() as $row)
		{
			/* @var $row Row */
			
			$tr = "";
			foreach($tableView->getColumns() as $column)
			{
				/* @var $column ColumnInterface */
							
				$tr .= sprintf(
					"<td%s>%s</td>",
					RenderHelper::attrToString($column->getAttributes()),
					$column->getContent($row)
				);
			}
			
			$content .= sprintf("<tr%s>%s</tr>", RenderHelper::attrToString($row->getAttributes()), $tr);
		}
		
		if(count($tableView->getRows()) === 0)
		{
			$content .= sprintf(
				"<tr><td colspan=\"%s\">%s</td></tr>",
				count($tableView->getColumns()),
				$tableView->getEmptyValue()
			);
		}
		
		$content .= "</tbody>";
		
		return $content;
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
		return "</table>";
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
		$pagination = $tableView->getPagination();
		
		if($pagination === null)
		{
			return;
		}
		
		if($tableView->getTotalPages() < 2 && $pagination->getShowEmpty() === false)
		{
			return;
		}
		
		$strategy = StrategyFactory::getStrategy($tableView->getTotalPages(), $pagination->getMaxPages());
		/* @var $strategy StrategyInterface */ 
		$pages = $strategy->getPages($pagination->getCurrentPage(), $tableView->getTotalPages(), $pagination->getMaxPages());
		sort($pages);
		
		$classes = $pagination->getClasses();
		
		$ulClass = $classes['ul'] === null ? "" : sprintf(" class=\"%s\"", $classes['ul']);
		$content = sprintf("<ul%s>", $ulClass);
		
		// Left arrow.
		if($pagination->getCurrentPage() == 0)
		{
			$liClass = "";
			if($classes['li'] !== null || $classes['li_disabled'] !== null)
			{
				$liClass = sprintf(" class=\"%s %s\"", $classes['li'], $classes['li_disabled']);
			}
			$content .= sprintf("<li%s><a>%s</a></li>", $liClass, $pagination->getPreviousLabel());
		}
		else
		{
			$liClass = "";
			if($classes['li'] !== null)
			{
				$liClass = sprintf(" class=\"%s\"", $classes['li']);
			}
			
			$content .= sprintf(
				"<li%s><a href=\"%s\">%s</a></li>",
				$liClass,
				$this->urlHelper->getUrlForParameters(array(
					$pagination->getParameterName() => $pagination->getCurrentPage()
				), $tableView->getName()),
				$pagination->getPreviousLabel()
			);
		}
		
		// Pages
		for($pageIndex = 0; $pageIndex < count($pages); $pageIndex++)
		{
			$page = $pages[$pageIndex];
			$liClass = "";
			if($classes['li'] !== null || ($page == $pagination->getCurrentPage() && $classes['li_active'] !== null))
			{
				$liClass = sprintf(" class=\"%s %s\"", $classes['li'], $page == $pagination->getCurrentPage() ? $classes['li_active'] : '');
			}
			$content .= sprintf(
				"<li%s><a href=\"%s\">%s</a></li>",
				$liClass,
				$this->urlHelper->getUrlForParameters(array(
					$pagination->getParameterName() => $page + 1
				), $tableView->getName()),
				$page + 1
			);
			
			if($pageIndex+1 < count($pages) && $pages[$pageIndex+1]-$page > 1)
			{
				$liClass = "";
				if($classes['li'] !== null || $classes['li_disabled'] !== null)
				{
					$liClass = sprintf(" class=\"%s %s\"", $classes['li'], $classes['li_disabled']);
				}
				$content .= sprintf("<li%s><a>...</a></li>", $liClass,$page);
			}
		}
		
		// Right arrow.
		if($pagination->getCurrentPage() == $tableView->getTotalPages() - 1)
		{
			$liClass = "";
			if($classes['li'] !== null || $classes['li_disabled'] !== null)
			{
				$liClass = sprintf(" class=\"%s %s\"", $classes['li'], $classes['li_disabled']);
			}
			$content .= sprintf("<li%s><a>%s</a></li>", $liClass, $pagination->getNextLabel());
		}
		else
		{
			$liClass = "";
			if($classes['li'] !== null)
			{
				$liClass = sprintf(" class=\"%s\"", $classes['li']);
			}
			$content .= sprintf(
				"<li%s><a href=\"%s\">%s</a></li>",
				$liClass,
				$this->urlHelper->getUrlForParameters(array(
					$pagination->getParameterName() => $pagination->getCurrentPage() + 2
				), $tableView->getName()),
				$pagination->getNextLabel()
			);
		}
		
		$content .= "</ul>";
		
		return $content;
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
		$sortable = $tableView->getSortable();
		
		if(!$column->isSortable() || $sortable === null)
		{
			return $column->getLabel();
		}
		
		$isSortedColumn = $sortable->getColumnName() == $column->getName();
		if($isSortedColumn)
		{
			$direction = $sortable->getDirection() == 'asc' ? 'desc' : 'asc';
		}
		else
		{
			$direction = $sortable->getDirection();
		}
		
		$routeParams = array(
			$sortable->getParamColumnName() => $column->getName(),
			$sortable->getParamDirectionName() => $direction
		);
		
		$pagination = $tableView->getPagination();
		if($pagination !== null)
		{
			$routeParams[$pagination->getParameterName()] = 1;
		}

		$classes = $sortable->getClasses();
		
		return sprintf(
			"<a href=\"%s\">%s</a> %s",
			$this->urlHelper->getUrlForParameters($routeParams, $tableView->getName()),
			$column->getLabel(),
			$isSortedColumn ? sprintf("<span class=\"%s\"></span>", $classes[$sortable->getDirection()]) : ''
		);
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