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
use JGM\TableBundle\DependencyInjection\Service\TableHintService;
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
	const STATE_INSTANTIATED = 0;
	const STATE_PREPARED_FOR_BUILD = 1;
	const STATE_BUILD = 2;
	const STATE_DATA_LOADED = 4;

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
	 * Service for time measurement in
	 * debug mode.
	 * 
	 * @var TableStopwatchService
	 */
	private $stopwatchService;
	
	/**
	 * Service for creating hints
	 * in debug mode,
	 * 
	 * @var TableHintService
	 */
	private $hintService;
	
	/**
	 * @var TableContext 
	 */
	private $tableContext;
		
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
	 * Cache for selected rows.
	 * 
	 * @var array
	 */
	private $selectedRowsCache;
	
	/**
	 * Array of filters.
	 * 
	 * @var array
	 */
	private $filters;
	
	/**
	 * DataSource for fetching 
	 * table data.
	 * 
	 * @var DataSourceInterface
	 */
	private $dataSource;
	
	/**
	 * Is a prefix for this
	 * table necessary because of
	 * a multi table response?
	 * 
	 * @var boolean
	 */
	private $usePrefix;
	
	/**
	 * Table view.
	 * 
	 * @var TableView
	 */
	private $view;
	
	/**
	 * State of the table.
	 * 
	 * @var int
	 */
	private $state = self::STATE_INSTANTIATED;
	
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
	function __construct(ContainerInterface $container, EntityManager $entityManager, Request $request, RouterInterface $router, $usePrefix, TableStopwatchService $stopwatchService, TableHintService $hintService)
	{
		// Save the parameters: Symfonys container, curent request,
		// url router and doctrines entityManager
		$this->container = $container;
		$this->entityManager = $entityManager;
		$this->request = $request;
		$this->router = $router;
		$this->usePrefix = $usePrefix;
		$this->stopwatchService = $stopwatchService;
		$this->hintService = $hintService;
		$this->tableContext = $container->get('jgm.table_context');
		
		// Set up rows, filters and optionsResolver
		// for the table type.
		$this->options = array();
		$this->rows = array();
		$this->columns = array();
		$this->filters = array();
	}
	
	/**
	 * Creates a buildable table instance by instantiating
	 * column/filter/selection builder and fetching data
	 * source from table type.
	 * 
	 * @param AbstractTableType $tableType	Type of the table.
	 * @param array $options				Options for the table.
	 * @return Table
	 */
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
		
		$this->tableContext->unregisterTable($this);
		
		$this->stopwatchService->stop($tableType->getName(), TableStopwatchService::CATEGORY_INSTANTIATION);
		
		return $this;
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
		}
		
		return $this->view;
	}
	
		
	/**
	 * Sets the request, which should be handled
	 * by the table.
	 * 
	 * @param Request $request
	 */
	public function handleRequest(Request $request)
	{
		if($this->isBuild())
		{
			TableException::canNotHandleRequestAfterBuild($this->getName());
		}
		
		$this->request = $request;
	}
	
	/**
	 * Returning all Entities, described by the data source,
	 * which is defined at the table type.
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
	
	/**
	 * Returning the name of the table,
	 * defined at the table type.
	 * 
	 * @return string|null
	 */
	public function getName()
	{
		if($this->tableType !== null)
		{
			return $this->tableType->getName();
		}
	
		return null;
	}
	/**
	 * Returning the selected rows.
	 * If the table is not a selection provider or
	 * there is now post request with selected rows,
	 * the returned array will be empty.
	 * 
	 * @return array
	 */
	public function getSelectedRows()
	{
		if($this->isSelectionProvider() === false || $this->isSelectionRequested() === false)
		{
			return array();
		}
		
		if($this->isBuild() === false)
		{
			$this->buildTable(true);
		}
		
		if($this->isDataLoaded() === false)
		{
			$this->rows = $this->loadData();
		}
		if(!is_array($this->selectedRowsCache))
		{
			$this->selectedRowsCache = array();
			foreach($this->rows as $row)
			{
				/* @var $row Row */
				if($row->isSelected())
				{
					$this->selectedRowsCache[] = $row;
				}
			}
		}
				
		return $this->selectedRowsCache;
	}
	
	/**
	 * Returning true, if the selection button with the
	 * given name was pressed by the user.
	 * 
	 * @param string $name	Name of the selection button.
	 * @return boolean
	 */
	public function isSelectionButtonPressed($name)
	{
		if($this->isSelectionProvider() == false || $this->isSelectionRequested() === false)
		{
			return false;
		}
		
		return $this->request->request->has(sprintf("selection_submit_%s", $name));
	}
	
	/**
	 * Returning the table view of this table,
	 * which is used for render the table at twig.
	 * 
	 * @return TableView
	 */
	public function getTableView()
	{
		return $this->view;
	}
	
	/**
	 * Returning the tables container.
	 * 
	 * @return ContainerInterface
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Returning the tables request.
	 * 
	 * @return Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Returning the tables router.
	 * 
	 * @return RouterInterface
	 */
	public function getRouter()
	{
		return $this->router;
	}
	
	/************************* PRIVATE FUNCTIONS ****************************************************************************************************************
	
	/**
	 * Returns a column identified by the name.
	 * 
	 * @param string $columnName Name of the column.
	 * @return ColumnInterface
	 */
	private function getColumn($columnName)
	{
		if(!array_key_exists($columnName, $this->columns))
		{
			TableException::noSuchColumn($this->getName(), $columnName);
		}
		
		return $this->columns[$columnName];
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
		if($this->isBuild())
		{
			return;
		}
		
		$this->tableContext->registerTable($this);
		$this->prepareTableForBuild($loadData);
		
		if(($loadData && $this->options['table'][TableOptions::LOAD_DATA]) === true)
		{
			$this->rows = $this->loadData();
		}
		
		if(	$this->options['table'][TableOptions::HIDE_EMPTY_COLUMNS] === true 
			&& $this->options['table'][TableOptions::TOTAL_ITEMS] > 0
		)
		{
			$this->columns = $this->hideEmptyColumns();
		}
		
		$this->state |= self::STATE_BUILD;
		$this->tableContext->unregisterTable($this);
	}
	
	/**
	 * Prepares the table for loading data.
	 * 
	 * @param boolean $loadData	Load data?
	 */
	private function prepareTableForBuild($loadData)
	{
		if($this->isPreparedForBuild())
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
		$this->options = $this->resolveOptions();
		$this->stopwatchService->stop($this->getName(), TableStopwatchService::CATEGORY_RESOLVE_OPTIONS);
		
		// Build the filters, if the table type implements 
		// the FilterInterface
		if($this->isFilterProvider())
		{
			$this->stopwatchService->start($this->getName(), TableStopwatchService::CATEGORY_BUILD_FILTER);
			$this->filters = $this->buildFilter();
			$this->stopwatchService->stop($this->getName(), TableStopwatchService::CATEGORY_BUILD_FILTER);
		}
		
		if($this->isSelectionProvider())
		{
			$this->tableType->buildSelectionButtons($this->selectionButtonBuilder);
		}
		
		$this->stopwatchService->start($this->getName(), TableStopwatchService::CATEGORY_LOAD_DATA);
		$this->options['table'][TableOptions::TOTAL_ITEMS] = $this->calculateTotalItems($loadData);
		$this->stopwatchService->stop($this->getName(), TableStopwatchService::CATEGORY_LOAD_DATA);
		
		$this->state |= self::STATE_PREPARED_FOR_BUILD;
	}
	
	protected function loadData()
	{
		if($this->isDataLoaded())
		{
			return;
		}

		$this->stopwatchService->start($this->getName(), TableStopwatchService::CATEGORY_LOAD_DATA);

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

		$isSelectionRequested = $this->isSelectionRequested();
		$requestedRows = $this->request->request->get("selection_column", array());
		$rows = array();
		foreach($data as $dataRow)
		{
			$row = new Row($dataRow, ++$count, $isSelectionRequested && in_array($dataRow->getId(), $requestedRows));
			$row->setAttributes( $this->tableType->getRowAttributes($row) );

			$rows[] = $row;
		}
		
		$this->stopwatchService->stop($this->getName(), TableStopwatchService::CATEGORY_LOAD_DATA);
		$this->state |= self::STATE_DATA_LOADED;
		
		return $rows;
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
		$options = array();
		
		// Resolve Options of the table.
		$optionsResolver = new TableOptionsResolver($this->container);
		$this->tableType->configureOptions($optionsResolver);
		$options['table'] = $optionsResolver->resolve($this->options['table']);
		
		// Resolve options of pagination.
		if($this->tableType instanceof PaginationTypeInterface && $options['table'][TableOptions::USE_PAGINATION])
		{
			$options['pagination'] = $this->resolvePaginationOptions();
		}
		
		// Resolve sortable options.
		if($this->tableType instanceof OrderTypeInterface && $options['table'][TableOptions::USE_ORDER])
		{
			$options['order'] = $this->resolveOrderOptions();
		}
		
		// Resole filter options.
		if($this->tableType instanceof FilterTypeInterface && $options['table'][TableOptions::USE_FILTER])
		{
			$options['filter'] = $this->resolveFilterOptions();
		}
		
		return $options;
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
	private function calculateTotalItems($loadData = true)
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
				
				$this->validateCurrentPage($countPages);
				
				$this->options['pagination'][PaginationOptions::TOTAL_PAGES] = max($countPages, 1);
			}
			
			return $totalItems;
		}
		
		return 0;
	}
	
	/**
	 * Validates the current page, stored at the 
	 * pagination options. Throws a NotFoundHttpException,
	 * if the current page is lower than zero
	 * or greater than the page count.
	 * 
	 * @param int $countPages
	 */
	private function validateCurrentPage($countPages)
	{
		if(	$this->options['pagination'][PaginationOptions::CURRENT_PAGE] < 0 
			|| $this->options['pagination'][PaginationOptions::CURRENT_PAGE] > $countPages)
		{
			throw new NotFoundHttpException();
		}
	}

	/**
	 * Resolves the pagination-options from the current tableBuilder.
	 * 
	 * @return array
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
		
		// Check if the user made another decision for rows per page.
		$userRowsPerPage = $this->getUserRowsPerPage();
		if(in_array($userRowsPerPage, $pagination[PaginationOptions::OPTION_VALUES]))
		{
			$pagination[PaginationOptions::ROWS_PER_PAGE] = $userRowsPerPage;
		}
		
		// Read current page.
		$pagination[PaginationOptions::CURRENT_PAGE] = max(
			0, 
			((int) $this->request->get( $pagination[PaginationOptions::PARAM] )) - 1
		);
		
		return $pagination;		
	}
	
	/**
	 * Reads the users decision of rows per page
	 * from post parameter or cookie. If this is not
	 * happen, the returned value will be null.
	 * 
	 * @return int|null
	 */
	private function getUserRowsPerPage()
	{
		$userRowsPerPage = null;
		$itemsPerPageCookieName = sprintf("%s_items_per_page", $this->getName());
		$itemsPerPagePostName = sprintf("%s_option_values", $this->getName());
		if($this->request->isMethod('post') && $this->request->request->has($itemsPerPagePostName))
		{
			$userRowsPerPage = (int) $this->request->get($itemsPerPagePostName);
		}
		else if($this->request->cookies->has($itemsPerPageCookieName))
		{
			$userRowsPerPage = (int) $this->request->cookies->get($itemsPerPageCookieName);
		}
		
		return $userRowsPerPage;
	}
	
	/**
	 * Resolves the order options.
	 * 
	 * @return array
	 * @throws NotFoundHttpException
	 */
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
		$column = $this->request->get( $order[OrderOptions::PARAM_COLUMN], $order[OrderOptions::EMPTY_COLUMN] );
		$direction = $this->request->get( $order[OrderOptions::PARAM_DIRECTION] );

		// Find column and direction if the are empty.
		if($column === null)
		{
			$column = $this->getFirstOrderColumn();
		}
		$order[OrderOptions::CURRENT_COLUMN] = $column;

		if($direction === null)
		{
			$direction = $order[OrderOptions::EMPTY_DIRECTION];
		}
		$order[OrderOptions::CURRENT_DIRECTION] = $direction;
		
		// Require a sortable column, otherwise redirect to 404.
		if($this->getColumn($column)->isSortable() !== true)
		{
			throw new NotFoundHttpException();
		}

		return $order;
	}
	
	/**
	 * Finds the first sortable column of
	 * all specified columns.
	 * 
	 * @return string
	 */
	private function getFirstOrderColumn()
	{
		$this->hintService->addHint(
			$this->getName(),
			sprintf(
				'There is no default column for ordering the table. '
				. 'Defining a default column at order options with option'
				. ' "%s" will save time.', 
				OrderOptions::EMPTY_COLUMN
			)
		);
		
		// If no default column is defined, look for the first sortable.
		foreach($this->columns as $column)
		{
			/* @var $column ColumnInterface */

			if($column->isSortable() === true)
			{
				return $column->getName();
			}
		}

		TableException::noSortableColumn($this->getName());
	}
	
	/**
	 * Resolves the filter options.
	 * 
	 * @return array
	 */
	private function resolveFilterOptions()
	{		
		// Set button option default values.
		$filterOptionsResolver = new FilterOptionsResolver($this->container);
		
		// Set filter options.
		$this->tableType->configureFilterButtonOptions($filterOptionsResolver);

		// Set up the options container.
		return $filterOptionsResolver->resolve();
	}
	
	/**
	 * Returns the a list with all selection buttons,
	 * defined by the table type, if this is implementing
	 * the selection type interface.
	 * 
	 * @return array
	 */
	private function getSelectionButtons()
	{
		if($this->selectionButtonBuilder == null)
		{
			return array();
		}
		
		return $this->selectionButtonBuilder->getButtons();
	}
	
	/**
	 * Returns true, if the table type of this table
	 * implements the pagination type interface and the
	 * table options allowed the pagination usage.
	 * 
	 * @return boolean
	 */
	private function isPaginationProvider()
	{
		return $this->tableType instanceof PaginationTypeInterface && $this->options['table'][TableOptions::USE_PAGINATION];
	}
	
	/**
	 * Returns true, if the table type of this table
	 * implements the order type interface and the
	 * table options allowed the order usage.
	 * 
	 * @return boolean
	 */
	private function isOrderProvider()
	{
		return $this->tableType instanceof OrderTypeInterface && $this->options['table'][TableOptions::USE_ORDER];
	}
	
	/**
	 * Returns true, if the table type of this table
	 * implements the filter type interface and the
	 * table options allowed the filter usage.
	 * 
	 * @return boolean
	 */
	private function isFilterProvider()
	{
		return $this->tableType instanceof FilterTypeInterface && $this->options['table'][TableOptions::USE_FILTER];
	}
	
	/**
	 * Returns true, if the table type of this table
	 * implements the selection type interface and the
	 * table options allowed the selection usage.
	 * 
	 * @return boolean
	 */
	private function isSelectionProvider()
	{
		return $this->tableType instanceof SelectionTypeInterface && $this->options['table'][TableOptions::USE_SELECTION];
	}
	
	/**
	 * Prefix of this table for all request
	 * parameters.
	 * 
	 * @return string
	 */
	private function getPrefix()
	{
		return $this->tableType->getName() . '_';
	}
	
	/**
	 * Returns a new list of columns, in which
	 * the empty columns are removed.
	 * 
	 * @return array
	 */
	private function hideEmptyColumns()
	{
		$this->stopwatchService->start($this->getName(), TableStopwatchService::CATEGORY_HIDE_EMPTY_COLUMNS);
		
		$this->hintService->addHint(
			$this->getName(), 
			sprintf(
				'Hiding columns with no content is enabled at table options '
				. '(option "%s"). This option can be high priced.',
				TableOptions::HIDE_EMPTY_COLUMNS
			)
		);
		
		$columns = $this->columns;

		foreach($columns as $name => $column)
		{
			/* @var $column ColumnInterface */

			foreach($this->rows as $row)
			{
				/* @var $row Row */
				$content = $column->getContent($row);
				if($content !== null && strlen($content) > 0)
				{
					continue 2;
				}
			}

			unset($columns[$name]);
		}
		
		$this->stopwatchService->stop($this->getName(), TableStopwatchService::CATEGORY_HIDE_EMPTY_COLUMNS);
		
		return $columns;
	}
	
	/**
	 * Returns a list of the filters,
	 * specified by the table type.
	 * 
	 * @return array
	 */
	private function buildFilter()
	{
		$this->tableType->buildFilter($this->filterBuilder);
		$filters = $this->filterBuilder->getFilters();

		// Sets value of all filters.
		foreach($filters as $filter)
		{
			/* @var $filter FilterInterface */
			
			if($this->usePrefix)
			{
				$filter->setName(sprintf("%s%s", $this->getPrefix(), $filter->getName()));
			}

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
		
		return $filters;
	}
	
	/**
	 * Returns true, if this request includes selections
	 * for this table.
	 * 
	 * @return boolean
	 */
	private function isSelectionRequested()
	{
		$inputName = sprintf("is_selection_%s", $this->getName());
		return	$this->request->isMethod('post') 
				&& $this->request->request->has($inputName)
				&& $this->request->request->get($inputName) === $this->getName();
				
	}

	/**
	 * Returns true, if the table is prepared for build.
	 * 
	 * @return boolean
	 */
	private function isPreparedForBuild()
	{
		return ($this->state | 6) === 7;
	}
	
	/**
	 * Returns true, if the table is build.
	 * 
	 * @return boolean
	 */
	private function isBuild()
	{
		return ($this->state | 5) === 7;
	}
	
	/**
	 * Returns true, if the data of this table is laoded.
	 * 
	 * @return boolean
	 */
	private function isDataLoaded()
	{
		return ($this->state | 3) === 7;
	}
}
