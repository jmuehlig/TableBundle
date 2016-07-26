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
use JGM\TableBundle\DependencyInjection\Service\TableContext;
use JGM\TableBundle\DependencyInjection\Service\TableStopwatchService;
use JGM\TableBundle\Table\Column\ColumnInterface;
use JGM\TableBundle\Table\DataSource\DataSourceInterface;
use JGM\TableBundle\Table\Filter\FilterBuilder;
use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\Filter\OptionsResolver\FilterOptionsResolver;
use JGM\TableBundle\Table\Filter\Type\FilterTypeInterface;
use JGM\TableBundle\Table\OptionsResolver\TableOptions;
use JGM\TableBundle\Table\OptionsResolver\TableOptionsResolver;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Order\OptionsResolver\OrderOptions;
use JGM\TableBundle\Table\Order\OptionsResolver\OrderOptionsResolver;
use JGM\TableBundle\Table\Order\Type\OrderTypeInterface;
use JGM\TableBundle\Table\Pagination\Model\Pagination;
use JGM\TableBundle\Table\Pagination\OptionsResolver\PaginationOptions;
use JGM\TableBundle\Table\Pagination\OptionsResolver\PaginationOptionsResolver;
use JGM\TableBundle\Table\Pagination\Type\PaginationTypeInterface;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Table\Selection\SelectionButtonBuilder;
use JGM\TableBundle\Table\Selection\Type\SelectionTypeInterface;
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
	 * Builder for selection buttons.
	 * 
	 * @var SelectionButtonBuilder
	 */
	protected $selectionButtonBuilder;

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
	 * @var TableStopwatchService
	 */
	private $stopwatchService;
	
	/**
	 * @var TableContext 
	 */
	private $tableContext;
	
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
	 * List of columns, added to 
	 * the table type.
	 *  
	 * @var array
	 */
	private $columns;
	
	/**
	 * Array of all rows.
	 * 
	 * @var array 
	 */
	private $rows;
	
	/**
	 * Array of filters.
	 * 
	 * @var array
	 */
	private $filters;
	
	/**
	 * @var DataSourceInterface
	 */
	private $dataSource;
	
	/**
	 * State of this table: is the table prepared 
	 * for for huilding the table view?
	 * 
	 * @var boolean
	 */
	private $isPreparedForBuild = false;
	
	/**
	 * State of this table: is the table 
	 * already build?
	 * 
	 * @var boolean
	 */
	private $isBuild = false;
	
	/**
	 * State of this table: is the data
	 * already loaded?
	 * 
	 * @var boolean
	 */
	private $isDataLoaded = false;
	
	/**
	 * Is a prefix for this
	 * table necessary because of
	 * a multi table response?
	 * 
	 * @var boolean
	 */
	private $usePrefix;
	
	/**
	 * @var TableView
	 */
	private $view;
	
	/**
	 * Creates a new instance of an table.
	 * 
	 * @param ContainerInterface $container				Container.
	 * @param EntityManager $entityManager				Entity Manager.
	 * @param Request $request							Current request.
	 * @param RouterInterface $router					Router.
	 * @param boolean $usePrefix						Should the table use a prefix for filter, pagenination and order?
	 * @param TableStopwatchService $stopwatchService	Stopwatch-Service.
	 */
	function __construct(ContainerInterface $container, EntityManager $entityManager, Request $request, RouterInterface $router, LoggerInterface $logger, $usePrefix = false, TableStopwatchService $stopwatchService = null)
	{
		// Save the parameters: Symfonys container, curent request,
		// url router and doctrines entityManager
		$this->container = $container;
		$this->entityManager = $entityManager;
		$this->request = $request;
		$this->router = $router;
		$this->usePrefix = $usePrefix;
		$this->stopwatchService = $stopwatchService;
		$this->tableContext = $container->get('jgm.table_context');
		
		// Set up rows, filters and optionsResolver
		// for the table type.
		$this->options = array();
		$this->rows = array();
		$this->columns = array();
		$this->filters = array();
	}
	
	public function create(AbstractTableType $tableType, array $options = array())
	{
		$this->options['table'] = $options;
		
		$this->stopwatchService->start($tableType->getName(), TableStopwatchService::CATEGORY_INSTANTIATION);
		$this->tableBuilder = new TableBuilder($this->container);
		$this->tableType = $tableType;
		$this->dataSource = $tableType->getDataSource($this->container);
		
		$this->tableContext->registerTable($this);
		if($this->tableType instanceof FilterTypeInterface)
		{
			$this->filterBuilder = new FilterBuilder($this->container);
		}
		
		if($this->tableType instanceof SelectionTypeInterface) 
		{
			$this->selectionButtonBuilder = new SelectionButtonBuilder();
		}
		
		$this->tableType->setContainer($this->container);
		$this->tableType->setEntityManager($this->entityManager);
		
		$this->container->get('jgm.table_context')->unregisterTable($this);
		
		$this->stopwatchService->stop($tableType->getName(), TableStopwatchService::CATEGORY_INSTANTIATION);
		
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
		if(!array_key_exists($columnName, $this->columns))
		{
			TableException::noSuchColumn($this->getName(), $columnName);
		}
		
		return $this->columns[$columnName];
	}
	
	/**
	 * Creates a table renderer, rendering this table.
	 * 
	 * @return View of the table.
	 */
	public function createView($loadData = true)
	{
		
		if($loadData !== true)
		{
			 @trigger_error(
				'The signatur ($loadData) of Table::createView is deprecated since v1.2 and will be removed in 1.4. Use table option named "load_data".',
				E_USER_DEPRECATED
			);
		}
		
		if($this->view === null)
		{
			$this->tableContext->registerTable($this);
			$this->buildTable($loadData);
			$this->stopwatchService->start($this->getName(), TableStopwatchService::CATEGORY_BUILD_VIEW);
			$this->view = new TableView(
				$this->tableType->getName(),
				$this->options,
				$this->columns,
				$this->rows,
				$this->filters,
				$this->getSelectionButtons()
			);
			$this->stopwatchService->stop($this->getName(), TableStopwatchService::CATEGORY_BUILD_VIEW);
			$this->tableContext->unregisterTable($this);
		}
		return $this->view;
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
		$this->prepareTableForBuild($loadData);
		
		if(($loadData && $this->options['table'][TableOptions::LOAD_DATA]) === true)
		{
			$this->stopwatchService->start($this->getName(), TableStopwatchService::CATEGORY_LOAD_DATA);
			$this->loadData();
			$this->stopwatchService->stop($this->getName(), TableStopwatchService::CATEGORY_LOAD_DATA);
		}
		
		if(	$this->options['table'][TableOptions::HIDE_EMPTY_COLUMNS] === true 
			&& $this->options['table'][TableOptions::HIDE_EMPTY_COLUMNS] > 0
		)
		{
			$this->stopwatchService->start($this->getName(), TableStopwatchService::CATEGORY_HIDE_EMPTY_COLUMNS);
			$this->hideEmptyColumns();
			$this->stopwatchService->stop($this->getName(), TableStopwatchService::CATEGORY_HIDE_EMPTY_COLUMNS);
		}
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
		$this->stopwatchService->start($this->getName(), TableStopwatchService::CATEGORY_BUILD_TABLE);
		$this->tableType->buildTable($this->tableBuilder);
		$this->stopwatchService->stop($this->getName(), TableStopwatchService::CATEGORY_BUILD_TABLE);
		$this->columns = $this->tableBuilder->getColumns();
		
		// Resolve all options, defined in the table type.
		$this->stopwatchService->start($this->getName(), TableStopwatchService::CATEGORY_RESOLVE_OPTIONS);
		$this->resolveOptions();
		$this->stopwatchService->stop($this->getName(), TableStopwatchService::CATEGORY_RESOLVE_OPTIONS);
		
		// Build the filters, if the table type implements 
		// the FilterInterface
		if($this->isFilterProvider())
		{
			$this->stopwatchService->start($this->getName(), TableStopwatchService::CATEGORY_BUILD_FILTER);
			$this->buildFilter();
			$this->stopwatchService->stop($this->getName(), TableStopwatchService::CATEGORY_BUILD_FILTER);
		}
		
		if($this->isSelectionProvider())
		{
			$this->tableType->buildSelectionButtons($this->selectionButtonBuilder);
		}
		
		$this->stopwatchService->start($this->getName(), TableStopwatchService::CATEGORY_LOAD_DATA);
		$this->options['table'][TableOptions::TOTAL_ITEMS] = $this->calculateTotalItems($loadData);
		$this->stopwatchService->stop($this->getName(), TableStopwatchService::CATEGORY_LOAD_DATA);
		
		$this->isPreparedForBuild = true;
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
		if($this->isPaginationProvider())
		{
			$count = $this->options['pagination'][PaginationOptions::CURRENT_PAGE] 
					 * $this->options['pagination'][PaginationOptions::ROWS_PER_PAGE]; 
		}

		// Store the data items as Row-Object in the $rows class var.
		// Additional increment the counter for each row.
		$order = $this->isOrderProvider() ? new Order($this->options['order']) : null;
		$pagination = $this->isPaginationProvider() ? new Pagination($this->options['pagination']) : null;
		$data = $this->dataSource->getData(	$this->container, $this->columns, $this->filters, $pagination, $order );

		foreach($data as $dataRow)
		{
			$row = new Row($dataRow, ++$count);
			$row->setAttributes( $this->tableType->getRowAttributes($row) );

			$this->rows[] = $row;
		}

		$this->isDataLoaded = true;
	}
	
	/**
	 * Resolves the table options by defining some
	 * default options and passing the resolver to the
	 * table type.
	 * 
	 * Options are stored in the $options class var.
	 */
	protected function resolveOptions()
	{
		$this->options = array();
		
		// Resolve Options of the table.
		$optionsResolver = new TableOptionsResolver($this->container);
		$this->tableType->configureOptions($optionsResolver);
		$this->options['table'] = $optionsResolver->resolve();
		
		// Resolve options of pagination.
		if($this->isPaginationProvider())
		{
			$this->options['pagination'] = $this->resolvePaginationOptions();
		}
		
		// Resolve sortable options.
		if($this->isOrderProvider())
		{
			$this->options['order'] = $this->resolveOrderOptions();
		}
		
		// Resole filter options.
		if($this->isFilterProvider())
		{
			$this->options['filter'] = $this->resolveFilterOptions();
		}
	}
	
	/**
	 * Calculates the number of total items and configures
	 * the current page and total pages of the pagination 
	 * component.
	 * 
	 * @param boolean $loadData
	 * 
	 * @throws NotFoundHttpException	If the given current page is unavailable.
	 */
	protected function calculateTotalItems($loadData = true)
	{
		if($loadData === true && $this->options['table'][TableOptions::LOAD_DATA] === true)
		{
			// Read total items.
			$totalItems = $this->dataSource->getCountItems(
				$this->container,
				$this->columns,
				$this->filters
			);

			// Read total pages.
			if($this->isPaginationProvider())
			{
				$countPages = ceil($totalItems / $this->options['pagination'][PaginationOptions::ROWS_PER_PAGE]);
				
				if(	$this->options['pagination'][PaginationOptions::CURRENT_PAGE] < 0 
					|| $this->options['pagination'][PaginationOptions::CURRENT_PAGE] > $countPages)
				{
					throw new NotFoundHttpException();
				}

				$this->options['pagination'][PaginationOptions::TOTAL_PAGES] = max($countPages, 1);
			}
			
			return $totalItems;
		}
		
		return 0;
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
		$pagination = $paginationOptionsResolver->resolve();
		if($this->usePrefix)
		{
			$pagination[PaginationOptions::PARAM] = sprintf("%s%s", $this->getPrefix(), PaginationOptions::PARAM);
		}
		
		$userItemsPerPage = null;
		$itemsPerPageCookieName = sprintf("%s_items_per_page", $this->getName());
		$itemsPerPagePostName = sprintf("%s_option_values", $this->getName());
		if($this->request->isMethod('post') && $this->request->request->has($itemsPerPagePostName))
		{
			$userItemsPerPage = (int) $this->request->get($itemsPerPagePostName);
		}
		else if($this->request->cookies->has($itemsPerPageCookieName))
		{
			$userItemsPerPage = (int) $this->request->cookies->get($itemsPerPageCookieName);
		}
		
		if(in_array($userItemsPerPage, $pagination[PaginationOptions::OPTION_VALUES]))
		{
			$pagination[PaginationOptions::ROWS_PER_PAGE] = $userItemsPerPage;
		}
		
		// Read current page.
		$pagination[PaginationOptions::CURRENT_PAGE] = max(
			0, 
			((int) $this->request->get( $pagination[PaginationOptions::PARAM] )) - 1
		);
		
		return $pagination;		
	}
	
	private function resolveOrderOptions()
	{		
		// Configure the options resolver for the order options.
		$sortableOptionsResolver = new OrderOptionsResolver($this->container);
		$this->tableType->configureOrderOptions($sortableOptionsResolver);
		
		$order = $sortableOptionsResolver->resolve();
		if($this->usePrefix)
		{
			$order[OrderOptions::PARAM_COLUMN] = sprintf("%s%s", $this->getPrefix(), $order[OrderOptions::PARAM_COLUMN]);
			$order[OrderOptions::PARAM_DIRECTION] = sprintf("%s%s", $this->getPrefix(), $order[OrderOptions::PARAM_DIRECTION]);
		}
		
		// Read the column and direction from $request-object.
		$column = $this->request->get( $order[OrderOptions::PARAM_COLUMN] );
		$direction = $this->request->get( $order[OrderOptions::PARAM_DIRECTION] );

		// Find column and direction if the are empty.
		if($column === null)
		{
			if($order[OrderOptions::EMPTY_COLUMN] !== null)
			{
				$column = $order[OrderOptions::EMPTY_COLUMN];
			}
			else
			{
				// If no default column is defined, look for the first sortable.
				foreach($this->columns as $tmpColumn)
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
					TableException::noSortableColumn($this->getName());
				}
			}
		}
		$order[OrderOptions::CURRENT_COLUMN] = $column;

		if($direction === null)
		{
			$direction = $order[OrderOptions::EMPTY_DIRECTION];
		}
		$order[OrderOptions::CURRENT_DIRECTION] = $direction;
		
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
		return $filterOptionsResolver->resolve();
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
			$this->columns,
			$isFiltered ? $this->filters : null,
			null, 
			$isOrdered ? new Order($this->options['order']) : null
		);
	}
	
	private function getSelectionButtons()
	{
		if($this->selectionButtonBuilder == null)
		{
			return array();
		}
		
		return $this->selectionButtonBuilder->getButtons();
	}
	
	private function isPaginationProvider()
	{
		return $this->tableType instanceof PaginationTypeInterface && $this->options['table'][TableOptions::USE_PAGINATION];
	}
	
	private function isOrderProvider()
	{
		return $this->tableType instanceof OrderTypeInterface && $this->options['table'][TableOptions::USE_ORDER];
	}
	
	private function isFilterProvider()
	{
		return $this->tableType instanceof FilterTypeInterface && $this->options['table'][TableOptions::USE_FILTER];
	}
	
	private function isSelectionProvider()
	{
		return $this->tableType instanceof SelectionTypeInterface;
	}
	
	private function getPrefix()
	{
		return $this->tableType->getName() . '_';
	}
	
	public function getName()
	{
		if($this->tableType !== null)
		{
			return $this->tableType->getName();
		}
	
		return null;
	}
	
	public function handleRequest(Request $request)
	{
		if($this->isBuild)
		{
			TableException::canNotHandleRequestAfterBuild($this->getName());
		}
		
		$this->request = $request;
	}
	
	public function getTableView()
	{
		return $this->view;
	}
	
	private function hideEmptyColumns()
	{
		foreach($this->columns as $name => $column)
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
		$this->columns = $this->tableBuilder->getColumns();
	}
	
	private function buildFilter()
	{
		$this->tableType->buildFilter($this->filterBuilder);
		$this->filters = $this->filterBuilder->getFilters();
		if($this->usePrefix)
		{
			foreach($this->filters as $filter)
			{
				/* @var $filter FilterInterface */
				$filter->setName(sprintf("%s%s", $this->getPrefix(), $filter->getName()));
			}
		}

		// Sets value of all filters.
		foreach($this->filters as $filter)
		{
			/* @var $filter FilterInterface */

			$values = array();

			foreach($filter->getParameterNames() as $parameterName)
			{
				$requestParameterName = $parameterName;
				/*
				* The Request replaces '.' with '_' in parameter names. So we have
				* to do the same replacement, otherwise the parameter and it's value will get lost.
				*/
				if(strpos($parameterName,'.') !== false)
				{
					$requestParameterName = str_replace('.','_',$parameterName);
				}
				$values[$parameterName] = trim((string) $this->request->query->get($requestParameterName, ''));
			}

			$filter->setValue($values);
		}
	}
}
