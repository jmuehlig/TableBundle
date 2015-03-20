<?php

namespace JGM\TableBundle\Table\DataSource;

use JGM\TableBundle\Table\Model\PaginationOptionsContainer;
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
		
	}
	
	public function getData(ContainerInterface $container, array $columns, $filters = null, PaginationOptionsContainer $pagination = null, SortableOptionsContainer $sortable = null)
	{
		
	}
}
