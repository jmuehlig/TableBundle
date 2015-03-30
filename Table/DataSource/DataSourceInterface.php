<?php

namespace JGM\TableBundle\Table\DataSource;

use JGM\TableBundle\Table\Model\SortableOptionsContainer;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Pagination\Model\Pagination;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Interface for datasources, used by the table
 * for filling it with data.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
interface DataSourceInterface
{
	/**
	 * Creates an array with data for the table.
	 * 
	 * @param ContainerInterace					$container	Symfonys container.
	 * @param array								$columns	Array with all columns of the table.
	 * @param array|null						$filters	Array with all filters of the table, null if filters are not supported.
	 * @param Pagination|null					$pagination	Container with all pagination options, null if pagination is not supported.
	 * @param SortableOptionsContainer|null		$sortable	Container with all sorting options, null if sorting is not supported.
	 * 
	 * @return array							Array with data for the table.
	 */
	public function getData(
		ContainerInterface $container,
		array $columns,
		array $filters = null,
		Pagination $pagination = null,
		Order $sortable = null
	);
	
	/**
	 * Returns the number of items.
	 * 
	 * @param ContainerInterace					$container	Symfonys container.
	 * @param array								$columns	Array with all columns of the table.
	 * @param array|null						$filters	Array with all filters of the table, null if filters are not supported.
	 * 
	 * @return array							Array with data for the table.
	 */
	public function getCountItems(
		ContainerInterface $container,
		array $columns,
		array $filters = null
	);
}
