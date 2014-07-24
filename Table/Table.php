<?php

namespace PZAD\TableBundle\Table;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use PZAD\TableBundle\Table\Column\ColumnInterface;
use PZAD\TableBundle\Table\Type\AbstractTableType;
use PZAD\TableBundle\Table\Type\PaginatableInterface;
use PZAD\TableBundle\Table\Type\SortableInterface;

/**
 * The table forms the core class of the bundle.
 * It will be build by the table builder and represented
 * by the table view.
 * 
 * @author Jan Mühlig <mail@janmuehlig.de>
 * @since 1.0.0
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
	 * @var Filter\FilterBuilder
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
	 * @var array 
	 */
	private $pagination;
	
	/**
	 * Rehased sort information.
	 * NULL, if sort is disabled.
	 * 
	 * @var array
	 */
	private $sortable;
	
	/**
	 * Used filters of this table:
	 * name => value.
	 * Empty array, if type of the table
	 * does not implement the FilterInterface.
	 * 
	 * @var array
	 */
	private $filters;
	
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
		$this->filters = array();
		$this->options = array();
	}
	
	public function create(AbstractTableType $tableType)
	{
		$this->tableBuilder = new TableBuilder($this->container);
		
		if($tableType instanceof Filter\FilterInterface)
		{
			$this->filterBuilder = new Filter\FilterBuilder($this->entityManager);
		}
		
		$this->tableType = $tableType;
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
	
	public function getRowAttributes(Row\Row $row)
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
	 * @return TableRenderer Table renderer.
	 */
	public function createView()
	{
		$this->buildTable();
		
		return new TableView(
			$this->tableType->getName(),
			$this->tableBuilder->getColumns(),
			$this->rows,
			$this->filters,
			$this->pagination,
			$this->sortable,
			$this->options['empty_value'],
			$this->options['attr'],
			$this->options['head_attr']
		);
	}
	
	/**
	 * Builds the table by processiong the tableBuilder
	 * and fetching all rows.
	 * Last are stored in the rows-array.
	 */
	private function buildTable()
	{
		// Resolve all options, defined in the table type.
		$this->resolveOptions();
		
		// Build the type (adding all columns).
		$this->tableType->buildTable($this->tableBuilder);
		
		// Build the filters, if the table type implements 
		// the FilterInterface
		if($this->tableType instanceof Filter\FilterInterface)
		{
			$this->tableType->buildFilter($this->filterBuilder);
		}
		
		// Fetch data from the database.
		$data = $this->getData();
		
		// Initialise the row counter, raise the counter,
		// if the table uses pagination.
		// For example, the counter should start at 11, if 
		// the table is on page 2 and uses 10 rows per page.
		$count = 0;
		if(count($this->pagination) > 0)
		{
			$count = $this->pagination['page'] * $this->pagination['rows_per_page'];
		}

		// Store the data items as Row-Object in the $rows class var.
		// Additional increment the counter for each row.
		foreach($data as $object)
		{
			$row = new Row\Row($object, ++$count);
			$row->setAttributes( $this->tableType->getRowAttributes($row) );
			
			$this->rows[] = $row;
		}
				
		// Build the filters, if the type supports the filter interface.
		// TODO: Refactor
//		if($this->tableType instanceof Filter\FilterInterface)
//		{
//			$this->tableType->buildFilter($this->filterBuilder);
//			foreach($this->filterBuilder->getFilters() as $filter)
//			{
//				/* @var $filter Filter\Filter */
//				$column = $this->getColumn($filter->getColumnName());
//				if($column instanceof Column\EntityColumn && is_string($filter->getValues()))
//				{
//					$repository = $this->entityManager->getRepository($filter->getValues());
//					$values = array();
//					foreach($repository->findAll() as $item)
//					{
//						$values[$item->getId()] = $item;
//					}
//					
//					$filter->setValues($values);
//				}
//			}
//		}
//		
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
		$optionsResolver = new OptionsResolver();
		
		// Set the required options for the table type.
		$optionsResolver->setRequired(array('data_entity'));
		
		// Set the defailt options for the table type.
		$optionsResolver->setDefaults(array(
			'empty_value' => 'No data found.',
			'attr' => array(),
			'head_attr' => array(),
			'pagination' => false,
			'sortable' => false
		));
		
		$this->tableType->setDefaultOptions($optionsResolver);
		
		$this->options = $optionsResolver->resolve(array());
	}


	/**
	 * Building the data iterator by executing the 
	 * tableType.getQuery method and using pagination
	 * and sort, if they are enabled.
	 * 
	 * @return \Iterator
	 */
	private function getData()
	{
		// Build the QueryBuilder and give the table type
		// the chance to refine the query, e.g. with where-conditions.
		$queryBuilder = $this->entityManager->createQueryBuilder();
		$this->tableType->buildQuery(
			$queryBuilder,
			$this->tableBuilder->getColumns(),
			$this->options['data_entity']
		);
		$this->tableType->refineQuery($queryBuilder);
		
		// Apply sortable to the query builder.
		$this->applySortable($queryBuilder);
		
		// Apply filters to the query builder.
		$this->applyFilters($queryBuilder);
		
		// Apply pagination to the query builder and
		// return a paginator, if pagination is in use.
		if($this->applyPagination($queryBuilder) === true)
		{
			return new Paginator($queryBuilder->getQuery(), $fetchJoinCollection = false);
		}
		
		// If pagination is not in use, return the query with all results.
		return $queryBuilder->getQuery()->getResult();
	}
	
	/**
	 * Sets up the sortable part to the QueryBuilder.
	 * 
	 * @param	QueryBuilder $queryBuilder	Applies the sortable query options
	 *										to the query builder, also used by
	 *										the table type.
	 */
	protected function applySortable(QueryBuilder $queryBuilder)
	{
		// Set up the query builder.
		if($this->resolveSortableOptions() === true)
		{
			$queryBuilder->orderBy(sprintf('t.%s', $this->sortable['column']), $this->sortable['direction']);
		}
	}
	
	/**
	 * Sets up the pagination part to the QueryBuilder.
	 * 
	 * @param	QueryBuilder $queryBuilder	Applies the pagination query options
	 *										to the query builder, also used by
	 *										the table type.
	 * 
	 * @return boolean						True, if the pagination was applied (if
	 *										and only if pagination is in use).
	 *										False, otherwise.
	 */
	protected function applyPagination(QueryBuilder $queryBuilder)
	{
		// Set up the query builder, if pagination is in use.
		if($this->resolvePaginationOptions() === true)
		{
			$countQuery = $this->entityManager->createQueryBuilder();
			$this->tableType->buildQuery(
				$countQuery,
				$this->tableBuilder->getColumns(),
				$this->options['data_entity']
			);
			$this->tableType->refineQuery($countQuery);
			
			$countItems = $countQuery->select('count(t)')->getQuery()->getSingleScalarResult();
			$countPages = ceil($countItems / $this->pagination['rows_per_page']);
			if($countPages < 1)
			{
				$countPages = 1;
			}
			$this->pagination['count_pages'] = $countPages;

			if($this->pagination['page'] < 0 || $this->pagination['page'] > $countPages - 1)
			{
				throw new NotFoundHttpException();
			}

			$queryBuilder
				->setFirstResult($this->pagination['page'] * $this->pagination['rows_per_page'])
				->setMaxResults($this->pagination['rows_per_page']);

			return true;
		}
		
		return false;
	}
	
	/**
	 * TODO: Refactor.
	 * Sets up the filter part to the QueryBuilder.
	 * 
	 * @param	QueryBuilder $queryBuilder	Applies the filter query options
	 *										to the query builder, also used by
	 *										the table type.
	 */
	protected function applyFilters(QueryBuilder $queryBuilder)
	{
		if($this->tableType instanceof Filter\FilterInterface)
		{
			$this->resolveFilterOptions();
			$filters = $this->getFilters();
			$isFirstWhere = true;
			
			foreach($this->filters as $filterName => $filterValue)
			{
				$filter = $filters[$filterName];
				/* @var $filter Filter\Filter */
				
				if($filter->getValues() !== null && is_array($filter->getValues()) && count($filter->getValues()) > 0)
				{
					$values = $filter->getValues();
					if(!array_key_exists($filterValue, $values))
					{
						continue;
					}
					$filterValue = $values[$filterValue];
				}
				
				$whereStatement = null;
				switch($filter->getOperator())
				{
					case Filter\FilterOperator::LIKE:
						$whereStatement = 't.%s like %:%s%'; break;
					case Filter\FilterOperator::LT:
						$whereStatement = 't.%s < :%s'; break;
					case Filter\FilterOperator::GT:
						$whereStatement = 't.%s > :%s'; break;
					case Filter\FilterOperator::LEQ:
						$whereStatement = 't.%s <= :%s'; break;
					case Filter\FilterOperator::GEQ:
						$whereStatement = 't.%s >= :%s'; break;
					case Filter\FilterOperator::NOT_EQ:
						$whereStatement = 't.%s <> :%s'; break;
					case Filter\FilterOperator::NOT_LIKE:
						$whereStatement = 't.%s not like %:%s%'; break;
					default: $whereStatement = 't.%s = :%s';
				}
				
				if($isFirstWhere)
				{
					$queryBuilder->where(sprintf($whereStatement, $filterName, $filterName));
					$isFirstWhere = false;
				}
				else
				{
					$queryBuilder->orWhere(sprintf($whereStatement, $filterName, $filterName));
				}
				$queryBuilder->setParameter($filterName, $filterValue);
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
	 * 
	 * @return boolean		True, if pagination is in use.
	 *						False, otherwise.
	 */
	private function resolvePaginationOptions()
	{
		// Only rehash the pagination options,
		// if pagination is used in the table type.
		if($this->tableType instanceof PaginatableInterface === false)
		{
			$this->pagination = array();
			return false;
		}
		
		// Configure the options resolver for the pagination.
		$paginationOptionsResolver = new OptionsResolver();
		$paginationOptionsResolver->setDefaults(array(
			'param' => 'page',
			'rows_per_page' => 20,
			'ul_class' => 'pagination',
			'li_class' => null,
			'li_class_active' => 'active',
			'li_class_disabled' => 'disabled'
		));
		
		// Set the defaults by the table type.
		$this->tableType->setPaginatableDefaultOptions($paginationOptionsResolver);
		
		// Resolve the options.
		$this->pagination = $paginationOptionsResolver->resolve(array());
		
		// Read the current page from $request-object.
		$this->pagination['page'] = ((int) $this->request->get( $this->pagination['param'] )) - 1;
		
		return true;
	}
	
	private function resolveSortableOptions()
	{
		// Only rehash the sortable options,
		// if sort is used in the table type.
		if($this->tableType instanceof SortableInterface === false)
		{
			$this->sortable = array();
			return false;
		}
		
		// Configure the options resolver for the sortable options.
		$sortableOptionsResolver = new OptionsResolver();
		$sortableOptionsResolver->setDefaults(array(
			'param_direction' => 'direction',
			'param_column' => 'column',
			'empty_direction' => 'desc',
			'empty_column' => null,
			'class_asc' => '',
			'class_desc' => ''
		));
		
		// Set the defaults by the table type.
		$this->tableType->setSortableDefaultOptions($sortableOptionsResolver);
		
		// Resolve the options.
		$this->sortable = $sortableOptionsResolver->resolve(array());
		
		// Read the column and direction from $request-object.
		$column = $this->request->get( $this->sortable['param_column'] );
		$direction = $this->request->get( $this->sortable['param_direction'] );
		
		if($column === null)
		{
			if($this->sortable['empty_column'] === null)
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
			else
			{
				$column = $this->sortable['empty_column'];
			}
		}
		
		if($direction === null)
		{
			$direction = $this->sortable['empty_direction'];
		}
		
		// Set the values of column and direction in the sortable options array.
		$this->sortable['column'] = $column;
		$this->sortable['direction'] = $direction;
		
		// Require a sortable column, otherwise redirect to 404.
		$sortedColumn = $this->getColumn($this->sortable['column']);
		if($sortedColumn->isSortable() !== true)
		{
			throw new NotFoundHttpException();
		}
		
		return true;
	}
	
	private function resolveFilterOptions()
	{
		foreach($this->getFilters() as $filter)
		{
			/* @var $filter Filter\Filter */
			
			$filterValue = $this->request->get($filter->getColumnName(), null);
			if($filterValue !== null)
			{
				$this->filters[$filter->getColumnName()] = trim($filterValue);
			}
		}
	}
}
