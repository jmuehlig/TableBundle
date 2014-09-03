<?php

namespace PZAD\TableBundle\Table\Renderer;

use PZAD\TableBundle\Table\TableView;

/**
 * Interface for table renderer.
 * 
 * @author	Jan Mühlig
 * @since	1.0.0
 */
interface RendererInterface
{
	/**
	 * Renders the begin of the table.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string	HTML output for the beginning of the table.
	 */
	public function renderBegin(TableView $tableView);
	
	/**
	 * Renders the head of the table.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string	HTML output for the head of the table.
	 */
	public function renderHead(TableView $tableView);
	
	/**
	 * Renders the body of the table.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string	HTML output for the body of the table.
	 */
	public function renderBody(TableView $tableView);
	
	/**
	 * Renders the end of the table.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string	HTML output for the end of the table.
	 */
	public function renderEnd(TableView $tableView);
	
	/**
	 * Renders the pagination of the table.
	 * 
	 * @param TableView $tableView	View of the table.
	 * 
	 * @return string	HTML output for the pagination of the table.
	 */
	public function renderPagination(TableView $tableView);
}
