<?php

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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * The table forms the core class of the bundle.
 * It will be build by the table builder and represented
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
	
	function __construct(ContainerInterface $container, EntityManager $entityManager, Request $request, RouterInterface $router)
	{
		// Save the parameters: Symfonys container, curent request,
		// url router and doctrines entityManager
		$this->container = $container;
		$this->entityManager = $entityManager;
		$this->request = $request;
		$this->router = $router;
		
		// Set up rows, filters and optionsResolver
		// for the table type.
		$this->rows = array();
		$this->options = array();
	}
	
	public function create(AbstractTableType $tableType)
	{
		$this->tableBuilder = new TableBuilder($this->container);
		$this->tableType = $tableType;
		$this->dataSource = $tableType->getDataSource($this->container);
		
		if($this->isFilterProvider())
		{
			$this->filterBuilder = new FilterBuilder($this->container);
		}
		
		$this->tableType->setContainer($this->container);
		$this->tableType->setEntityManager($this->entityManager);
		
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
	public function createView()
	{
		$this->buildTable();
		
		return new TableView(
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
	}
	
	/**
	 * Builds the table by processiong the tableBuilder
	 * and fetching all rows.
	 * Last are stored in the rows-array.
	 */
	private function buildTable()
	{		
		// Build the type (adding all columns).
		$this->tableType->buildTable($this->tableBuilder);

		// Build the filters, if the table type implements 
		// the FilterInterface
		if($this->isFilterProvider())
		{
			$this->tableType->buildFilter($this->filterBuilder);
		}
		
		// Resolve all options, defined in the table type.
		$this->resolveOptions();
		
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
	}
	
	/**
	 * Resolves the table type options by defining some
	 * default options and passing the resolver to the
	 * table type.
	 * 
	 * Options are stored in the $options class var.
	 */
	protected function resolveOptions()
	{
		// Resolve Options of the table.
		$optionsResolver = new TableOptionsResolver();
		$this->tableType->setDefaultOptions($optionsResolver);
		$this->options = $optionsResolver->resolve(array());
		
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
		$paginationOptionsResolver = new PaginationOptionsResolver();
		
		// Set the defaults by the table type.
		$this->tableType->setPaginationDefaultOptions($paginationOptionsResolver);
		
		// Setup options container.
		$pagination = $paginationOptionsResolver->toPagination();
		
		// Read current page.
		$currentPage = max(0, ((int) $this->request->get( $pagination->getParameterName() )) - 1);
		$pagination->setCurrentPage($currentPage);
		
		return $pagination;		
	}
	
	private function resolveOrderOptions()
	{		
		// Configure the options resolver for the order options.
		$sortableOptionsResolver = new OrderOptionsResolver();
		$this->tableType->setOrderDefaultOptions($sortableOptionsResolver);
		$order = $sortableOptionsResolver->toOrder();
		
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
		$filterOptionsResolver = new FilterOptionsResolver();
		
		// Set filter options.
		$this->tableType->setFilterButtonOptions($filterOptionsResolver);

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
}
