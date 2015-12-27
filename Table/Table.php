<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table;

use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\EntityManager;
use JGM\TableBundle\Table\Column\ColumnInterface;
use JGM\TableBundle\Table\DataSource\DataSourceInterface;
use JGM\TableBundle\Table\Filter\FilterBuilder;
use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\Filter\Model\Filter;
use JGM\TableBundle\Table\Filter\OptionsResolver\FilterOptionsResolver;
use JGM\TableBundle\Table\Filter\Type\FilterTypeInterface;
use JGM\TableBundle\Table\OptionsResolver\TableOptionsResolver;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Order\OptionsResolver\OrderOptionsResolver;
use JGM\TableBundle\Table\Order\Type\OrderTypeInterface;
use JGM\TableBundle\Table\Pagination\Model\Pagination;
use JGM\TableBundle\Table\Pagination\OptionsResolver\PaginationOptionsResolver;
use JGM\TableBundle\Table\Pagination\Type\PaginationTypeInterface;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Table\Type\AbstractTableType;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * The table will be build by the table builder and represented
 * by the table view.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class Table
{
	/**
	 * TableBuilder for this table.
	 * 
	 * @var TableBuilder 
	 */
	protected $tableBuilder;
	
	/**
	 * FilterBuilder for this table.
	 * 
	 * @var FilterBuilder
	 */
	protected $filterBuilder;
	
	/**
	 * Container.
	 * 
	 * @var ContainerInterface
	 */
	private $container;
	
	/**
	 * Current request.
	 * 
	 * @var Request 
	 */
	private $request;
	
	/**
	 * EntityManager.
	 * 
	 * @var EntityManager 
	 */
	private $entityManager;
	
	/**
	 * Router.
	 * 
	 * @var RouterInterface
	 */
	private $router;
	
	/**
	 * Logger.
	 * 
	 * @var LoggerInterface
	 */
	private $logger;
	
	/**
	 * Table type.
	 * 
	 * @var AbstractTableType 
	 */
	private $tableType;
	
	/**
	 * Options for the table type.
	 * 
	 * @var array
	 */
	private $options;
	
	/**
	 * Array of all rows.
	 * 
	 * @var array 
	 */
	private $rows;
	
	/**
	 * Rehashed pagination information.
	 * NULL, if pagination is disabled.
	 * 
	 * @var Pagination 
	 */
	private $pagination;
	
	/**
	 * Rehased order information.
	 * NULL, if order is disabled.
	 * 
	 * @var Order
	 */
	private $order;
	
	/**
	 * Rehashed filter information.
	 * NULL, if filter is disabled.
	 * 
	 * @var Filter
	 */
	private $filter;
	
	/**
	 * Number of total pages.
	 * 
	 * @var int
	 */
	private $totalPages = 1;
	
	/**
	 * Number of total items.
	 * 
	 * @var int
	 */
	private $totalItems;
	
	/**
	 * @var DataSourceInterface
	 */
	private $dataSource;
	
	private $isPreparedForBuild = false;
	
	private $isBuild = false;
	
	private $isDataLoaded = false;
	
	private $usePrefix;
	
	/**
	 * Creates a new instance of an table.
	 * 
	 * @param ContainerInterface $container	Container.
	 * @param EntityManager $entityManager	Entity Manager.
	 * @param Request $request				Current request.
	 * @param RouterInterface $router		Router.
	 * @param boolean $usePrefix			Should the table use a prefix for filter, pagenination and order?
	 */
	function __construct(ContainerInterface $container, EntityManager $entityManager, Request $request, RouterInterface $router, LoggerInterface $logger, $usePrefix = false)
	{
		// Save the parameters: Symfonys container, curent request,
		// url router and doctrines entityManager
		$this->container = $container;
		$this->entityManager = $entityManager;
		$this->request = $request;
		$this->router = $router;
		$this->logger = $logger;
		$this->usePrefix = $usePrefix;
		
		// Set up rows, filters and optionsResolver
		// for the table type.
		$this->rows = array();
	}
	
	public function create(AbstractTableType $tableType, array $options = array())
	{
		$this->logger->debug(sprintf("Start creating table, described by table type '%s'", get_class($tableType)));
		$this->container->get('jgm.table_context')->registerTable($this);
		
		$this->options = $options;
		
		$this->tableBuilder = new TableBuilder($this->container);
		$this->tableType = $tableType;
		$this->dataSource = $tableType->getDataSource($this->container);
		
		if($this->isFilterProvider())
		{
			$this->filterBuilder = new FilterBuilder($this->container);
		}
		
		$this->tableType->setContainer($this->container);
		$this->tableType->setEntityManager($this->entityManager);
		
		$this->container->get('jgm.table_context')->unregisterTable($this);
		$this->logger->debug(sprintf("Finished creating table, described by table type '%s'", get_class($tableType)));
		
		return $this;
	}
	
	/**
	 * Returns a column identified by the name.
	 * 
	 * @param string $columnName Name of the column.
	 * @return ColumnInterface
	 */
	public function getColumn($columnName)
	{
		$columns = $this->tableBuilder->getColumns();
		if(!array_key_exists($columnName, $columns))
		{
			TableException::noSuchColumn($columnName);
		}
		
		return $columns[$columnName];
	}
	
	/**
	 * Returns a filter identified by the name.
	 * 
	 * @param string $filterName Name of the column.
	 * @return FilterInterface
	 */
	public function getFilter($filterName)
	{
		$filters = $this->getFilters();
		if(!is_array($filters) || !array_key_exists($filterName, $filters))
		{
			TableException::noSuchFilter($filterName);
		}
		
		return $filters[$filterName];
	}
	
	public function getRowAttributes(Row $row)
	{
		$attr = $this->tableType->getRowAttributes($row);
		if(!is_array($attr))
		{
			return array();
		}
		
		return $attr;
	}
	
	/**
	 * Creates a table renderer, rendering this table.
	 * 
	 * @return View of the table.
	 */
	public function createView($loadData = true)
	{
		$this->logger->debug(sprintf("Start creating view, described by table type '%s'", get_class($this->tableType)));
		$this->container->get('jgm.table_context')->registerTable($this);
		
		$this->buildTable($loadData);
		
		$view = new TableView(
			$this->tableType->getName(),
			$this->tableBuilder->getColumns(),
			$this->rows,
			$this->getFilters(),
			$this->pagination,
			$this->order,
			$this->filter,
			$this->options['empty_value'],
			$this->options['attr'],
			$this->options['head_attr'],
			$this->totalPages,
			$this->totalItems
		);
		
		$this->container->get('jgm.table_context')->unregisterTable($this);
		$this->logger->debug(sprintf("Finished creating view, described by table type '%s'", get_class($this->tableType)));
		
		return $view;
	}
	
	/**
	 * Prepares the table for loading data.
	 * 
	 * @param boolean $loadData	Load data?
	 */
	private function prepareTableForBuild($loadData)
	{
		if($this->isPreparedForBuild)
		{
			return;
		}
		
		// Build the type (adding all columns).
		$this->tableType->buildTable($this->tableBuilder);

		// Build the filters, if the table type implements 
		// the FilterInterface
		if($this->isFilterProvider())
		{
			$this->tableType->buildFilter($this->filterBuilder);
			if($this->usePrefix)
			{
				foreach($this->getFilters() as $filter)
				{
					/* @var $filter FilterInterface */
					$filter->setName(sprintf("%s%s", $this->getPrefix(), $filter->getName()));
				}
			}
		}
		
		// Resolve all options, defined in the table type.
		$this->resolveOptions($loadData);
		
		$this->isPreparedForBuild = true;
	}
	
	/**
	 * Builds the table by processiong the tableBuilder
	 * and fetching all rows.
	 * Last are stored in the rows-array.
	 * 
	 * @param boolean $loadData	Load data?
	 */
	private function buildTable($loadData)
	{	
		if($this->isBuild)
		{
			return;
		}
		
		$this->prepareTableForBuild($loadData);
		
		if($loadData === true)
		{
			$this->loadData();
		}
		
		if($this->options['hide_empty_columns'] === true && $this->totalItems > 0)
		{
			foreach($this->tableBuilder->getColumns() as $name => $column)
			{
				/* @var $column ColumnInterface */

				foreach($this->rows as $row)
				{
					/* @var $row Row */
					$content = $column->getContent($row);
					if($content !== null && $content !== "")
					{
						continue 2;
					}
				}

				$this->tableBuilder->removeColumn($name);
			}
		}
		
		$this->isBuild = true;
	}
	
	protected function loadData()
	{
		if($this->isDataLoaded)
		{
			return;
		}
		// Initialise the row counter, raise the counter,
		// if the table uses pagination.
		// For example, the counter should start at 11, if 
		// the table is on page 2 and uses 10 rows per page.
		$count = 0;
		if($this->pagination !== null)
		{
			$count = $this->pagination->getCurrentPage() * $this->pagination->getItemsPerRow();
		}

		// Store the data items as Row-Object in the $rows class var.
		// Additional increment the counter for each row.
		$data = $this->dataSource->getData(
			$this->container,
			$this->tableBuilder->getColumns(),
			$this->getFilters(),
			$this->pagination, 
			$this->order
		);

		foreach($data as $dataRow)
		{
			$row = new Row($dataRow, ++$count);
			$row->setAttributes( $this->tableType->getRowAttributes($row) );

			$this->rows[] = $row;
		}

		$this->isDataLoaded = true;
	}
	
	/**
	 * Resolves the table type options by defining some
	 * default options and passing the resolver to the
	 * table type.
	 * 
	 * Options are stored in the $options class var.
	 * 
	 * @param boolean $loadData	Load data?
	 */
	protected function resolveOptions($loadData)
	{
		// Resolve Options of the table.
		$optionsResolver = new TableOptionsResolver($this->container);
		$this->tableType->configureOptions($optionsResolver);
		$this->options = $optionsResolver->resolve($this->options);
		
		// Resolve options of pagination.
		if($this->isPaginationProvider())
		{
			$this->pagination = $this->resolvePaginationOptions();
		}
		
		// Resolve sortable options.
		if($this->isOrderProvider())
		{
			$this->order = $this->resolveOrderOptions();
		}
		
		// Resole filter options.
		if($this->isFilterProvider())
		{
			$this->filter = $this->resolveFilterOptions();
		}
		
		if($loadData === true)
		{
			// Read total items.
			$this->totalItems = $this->dataSource->getCountItems(
				$this->container,
				$this->tableBuilder->getColumns(),
				$this->getFilters()
			);

			// Read total pages.
			if($this->pagination !== null)
			{
				$countPages = ceil($this->totalItems / $this->pagination->getItemsPerRow());
				if(	$this->pagination->getCurrentPage() < 0 || $this->pagination->getCurrentPage() > $countPages)
				{
					throw new NotFoundHttpException();
				}

				$this->totalPages = $countPages < 1 ? 1 : $countPages;
			}
		}
	}

	/**
	 * Builds the _pagination-array from the current tableBuilder.
	 * 
	 * Following keys are used:
	 *	rows_per_page:		Maximal num of items per page.
	 *	param:				Name of the request-parameter for the page.
	 *	page:				Current page.
	 *	classes:			Classes for rendering, containing classnames for "ul", "li", "li-active" and "li-disabled".
	 */
	private function resolvePaginationOptions()
	{	
		// Configure the options resolver for the pagination.
		$paginationOptionsResolver = new PaginationOptionsResolver($this->container);
		
		// Set the defaults by the table type.
		$this->tableType->configurePaginationOptions($paginationOptionsResolver);
		
		// Setup options container.
		$pagination = $paginationOptionsResolver->toPagination();
		if($this->usePrefix)
		{
			$pagination->setParameterName(sprintf("%s%s", $this->getPrefix(), $pagination->getParameterName()));
		}
		
		// Read current page.
		$currentPage = max(0, ((int) $this->request->get( $pagination->getParameterName() )) - 1);
		$pagination->setCurrentPage($currentPage);
		
		return $pagination;		
	}
	
	private function resolveOrderOptions()
	{		
		// Configure the options resolver for the order options.
		$sortableOptionsResolver = new OrderOptionsResolver($this->container);
		$this->tableType->configureOrderOptions($sortableOptionsResolver);
		$order = $sortableOptionsResolver->toOrder();
		if($this->usePrefix)
		{
			$order->setParamColumnName(sprintf("%s%s", $this->getPrefix(), $order->getParamColumnName()));
			$order->setParamDirectionName(sprintf("%s%s", $this->getPrefix(), $order->getParamDirectionName()));
		}
		
		// Read the column and direction from $request-object.
		$column = $this->request->get( $order->getParamColumnName() );
		$direction = $this->request->get( $order->getParamDirectionName() );

		// Find column and direction if the are empty.
		if($column === null)
		{
			if($order->getEmptyColumnName() !== null)
			{
				$column = $order->getEmptyColumnName();
			}
			else
			{
				// If no default column is defined, look for the first sortable.
				foreach($this->tableBuilder->getColumns() as $tmpColumn)
				{
					/* @var $tmpColumn ColumnInterface */

					if($tmpColumn->isSortable() === true)
					{
						$column = $tmpColumn->getName();
						break;
					}
				}
				
				if($column === null)
				{
					TableException::noSortableColumn();
				}
			}
		}
		$order->setCurrentColumnName($column);

		if($direction === null)
		{
			$direction = $order->getEmptyDirection();
		}
		$order->setCurrentDirection($direction);
		
		// Require a sortable column, otherwise redirect to 404.
		$sortedColumn = $this->getColumn($column);
		if($sortedColumn->isSortable() !== true)
		{
			throw new NotFoundHttpException();
		}

		return $order;
	}
	
	private function resolveFilterOptions()
	{		
		// Set button option default values.
		$filterOptionsResolver = new FilterOptionsResolver($this->container);
		
		// Set filter options.
		$this->tableType->configureFilterButtonOptions($filterOptionsResolver);

		// Set up the options container.
		$filterOptions = $filterOptionsResolver->toFilter();
		
		// Sets value of all filters.
		foreach($this->getFilters() as $filter)
		{
			/* @var $filter FilterInterface */
			
			$values = array();
			
			foreach($filter->getParameterNames() as $parameterName)
			{
				$values[$parameterName] = trim((string) $this->request->query->get($parameterName, ''));
			}
			
			$filter->setValue($values);
		}
		
		return $filterOptions;
	}
	
	public function getContainer()
	{
		return $this->container;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getRouter()
	{
		return $this->router;
	}
	
	/**
	 * Returning all Entities.
	 * 
	 * @param boolean $isFiltered	Should the entities be filtered by the filters?
	 * @param boolean $isOrdered	Should the entites ordered, like the table?
	 * 
	 * @return array
	 */
	public function getData($isFiltered = true, $isOrdered = true)
	{
		$this->prepareTableForBuild(true);
		
		return $this->dataSource->getData(
			$this->container,
			$this->tableBuilder->getColumns(),
			$isFiltered ? $this->getFilters() : null,
			null, 
			$isOrdered ? $this->order : null
		);
	}
	
	private function getFilters()
	{
		if($this->filterBuilder === null)
		{
			return array();
		}
		
		return $this->filterBuilder->getFilters();
	}
	
	private function isPaginationProvider()
	{
		return $this->tableType instanceof PaginationTypeInterface;
	}
	
	private function isOrderProvider()
	{
		return $this->tableType instanceof OrderTypeInterface;
	}
	
	private function isFilterProvider()
	{
		return $this->tableType instanceof FilterTypeInterface;
	}
	
	private function getPrefix()
	{
		return $this->tableType->getName() . '_';
	}
	
	public function handleRequest(Request $request)
	{
		if($this->isBuild)
		{
			TableException::canNotHandleRequestAfterBild();
		}
		
		$this->request = $request;
	}
}
