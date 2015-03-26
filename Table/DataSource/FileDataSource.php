<?php

namespace JGM\TableBundle\Table\DataSource;

use JGM\TableBundle\Table\Pagination\Model\Pagination;
use JGM\TableBundle\Table\Model\SortableOptionsContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * DataSource for rendering content of a file in a table.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class FileDataSource implements DataSourceInterface
{
	protected $file;
	
	public function __construct($file)
	{
		$this->file = $file;
	}
	
	public function getCountItems(ContainerInterface $container, array $columns, $filters = null)
	{
		// TODO
	}
	
	public function getData(ContainerInterface $container, array $columns, $filters = null, Pagination $pagination = null, SortableOptionsContainer $sortable = null)
	{
		// TODO
	}
}
