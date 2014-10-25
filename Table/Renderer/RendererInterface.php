<?php

namespace PZAD\TableBundle\Table\Renderer;

use PZAD\TableBundle\Table\Filter\FilterInterface;
use PZAD\TableBundle\Table\TableView;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Interface for table renderer.
 * 
 * @author	Jan Mühlig
 * @since	1.0.0
 */
interface RendererInterface
{
	public function __construct(ContainerInterface $container, Request $request, RouterInterface $router);
	
	/**
	 * Renders the begin of the table.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string	HTML output for the beginning of the table.
	 */
	public function renderTableBegin(TableView $tableView);
	
	/**
	 * Renders the head of the table.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string	HTML output for the head of the table.
	 */
	public function renderTableHead(TableView $tableView);
	
	/**
	 * Renders the body of the table.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string	HTML output for the body of the table.
	 */
	public function renderTableBody(TableView $tableView);
	
	/**
	 * Renders the end of the table.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string	HTML output for the end of the table.
	 */
	public function renderTableEnd(TableView $tableView);
	
	/**
	 * Renders the pagination of the table.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string	HTML output for the pagination of the table.
	 */
	public function renderTablePagination(TableView $tableView);
	
	/**
	 * Renders the begin of filters.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string	HTML output of the filters beginning.
	 */
	public function renderFilterBegin(TableView $tableView);
	
	/**
	 * Renders a single filter.
	 * 
	 * @param FilterInterface $filter	Single filter.
	 * 
	 * @return string					HTML output of this filter.
	 */
	public function renderFilter(FilterInterface $filter);
	
	/**
	 * Renders the label of a single filter.
	 * 
	 * @param FilterInterface $filter	Single filter.
	 * 
	 * @return string					HTML output of this filter label.
	 */
	public function renderFilterLabel(FilterInterface $filter);
	
	/**
	 * Renders the submit button for filters, using the form-enviroment.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string				HTML output of the submit button.
	 */
	public function renderFilterSubmitButton(TableView $tableView);
	
	/**
	 * Renders the reset link for filters.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string				HTML output of the reset link.
	 */
	public function renderFilterResetLink(TableView $tableView);
	
	/**
	 * Renders the end of the filters.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string				HTML output of the filters end.
	 */
	public function renderFilterEnd(TableView $tableView);
}
