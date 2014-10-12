<?php

namespace PZAD\TableBundle\Table;

use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\EntityManager;
use Iterator;
use PZAD\TableBundle\Table\Column\ColumnInterface;
use PZAD\TableBundle\Table\FilterBuilder;
use PZAD\TableBundle\Table\Filter\FilterInterface;
use PZAD\TableBundle\Table\Model\FilterOptionsContainer;
use PZAD\TableBundle\Table\Model\PaginationOptionsContainer;
use PZAD\TableBundle\Table\Model\SortableOptionsContainer;
use PZAD\TableBundle\Table\Renderer\DefaultRenderer;
use PZAD\TableBundle\Table\Row\Row;
use PZAD\TableBundle\Table\Type\AbstractTableType;
use PZAD\TableBundle\Table\Type\PaginatableInterface;
use PZAD\TableBundle\Table\Type\SortableInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

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
	 * @var PaginationOptionsContainer 
	 */
	private $pagination;
	
	/**
	 * Rehased sort information.
	 * NULL, if sort is disabled.
	 * 
	 * @var SortableOptionsContainer
	 */
	private $sortable;
	
	/**
	 * Rehashed filter information.
	 * NULL, if filter is disabled.
	 * 
	 * @var FilterOptionsContainer
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
		
		if($tableType instanceof Type\FilterableInterface)
		{
			$this->filterBuilder = new FilterBuilder($this->container);
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
			$this->options['renderer'],
			$this->tableBuilder->getColumns(),
			$this->rows,
			$this->getFilters(),
			$this->pagination,
			$this->sortable,
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
		if($this->tableType instanceof Type\FilterableInterface)
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
		$data = $this->tableType->getDataSource($this->container)->getData(
			$this->container,
			$this->tableBuilder->getColumns(),
			$this->getFilters(),
			$this->pagination, 
			$this->sortable
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
		$optionsResolver = new OptionsResolver();
		
		// Set the defailt options for the table type.
		$optionsResolver->setDefaults(array(
			'empty_value' => 'No data found.',
			'attr' => array(),
			'head_attr' => array(),
			'renderer' => new DefaultRenderer($this->container, $this->request, $this->router)
		));
		
		// Pass table type options.
		$this->tableType->setDefaultOptions($optionsResolver);
		
		// Allowed values.
		$optionsResolver->setAllowedTypes(array(
			'attr' => 'array',
			'head_attr' => 'array'
		));
		
		$this->options = $optionsResolver->resolve(array());
		
		// Resolve options of pagination.
		$this->resolvePaginationOptions();
		
		// Resolve sortable options.
		$this->resolveSortableOptions();
		
		// Resole filter options.
		$this->resolveFilterOptions();
		
		// Read total items.
		$this->totalItems = $this->tableType->getDataSource($this->container)->getCountItems(
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
	 * Building the data iterator by executing the 
	 * tableType.getQuery method and using pagination
	 * and sort, if they are enabled.
	 * 
	 * @return Iterator
	 */
	private function getData()
	{
		return $this->tableType->getDataSource($this->container);
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
		// Only rehash the pagination options,
		// if pagination is used in the table type.
		if($this->tableType instanceof PaginatableInterface === false)
		{
			$this->pagination = null;
			return;
		}
		
		// Configure the options resolver for the pagination.
		$paginationOptionsResolver = new OptionsResolver();
		$paginationOptionsResolver->setDefaults(array(
			'param' => 'page',
			'rows_per_page' => 20,
			'show_empty' => true,
			'ul_class' => 'pagination',
			'li_class' => null,
			'li_class_active' => 'active',
			'li_class_disabled' => 'disabled'
		));
		
		// Set the defaults by the table type.
		$this->tableType->setPaginatableDefaultOptions($paginationOptionsResolver);
		
		// Resolve the options.
		$pagination = $paginationOptionsResolver->resolve(array());
		
		// Setup options container.
		$this->pagination = new PaginationOptionsContainer(
			$pagination['param'],
			$pagination['rows_per_page'],
			((int) $this->request->get( $pagination['param'] )) - 1,
			$pagination['show_empty'],
			array(
				'ul' => $pagination['ul_class'],
				'li' => $pagination['li_class'],
				'li_active' => $pagination['li_class_active'],
				'li_disabled' => $pagination['li_class_disabled']
			)
		);
		
		
	}
	
	private function resolveSortableOptions()
	{
		// Only rehash the sortable options,
		// if sort is used in the table type.
		if($this->tableType instanceof SortableInterface === false)
		{
			$this->sortable = null;
			return;
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
		$sortable = $sortableOptionsResolver->resolve(array());
		
		// Read the column and direction from $request-object.
		$column = $this->request->get( $sortable['param_column'] );
		$direction = $this->request->get( $sortable['param_direction'] );
		
		// Find column and direction if the are empty.
		if($column === null)
		{
			if($sortable['empty_column'] === null)
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
				$column = $sortable['empty_column'];
			}
		}
		
		if($direction === null)
		{
			$direction = $sortable['empty_direction'];
		}
		
		// Require a sortable column, otherwise redirect to 404.
		$sortedColumn = $this->getColumn($column);
		if($sortedColumn->isSortable() !== true)
		{
			throw new NotFoundHttpException();
		}
		
		// Set up options container.
		$this->sortable = new SortableOptionsContainer(
			$sortable['param_direction'],
			$sortable['param_column'],
			$direction,
			$column,
			array('asc' => $sortable['class_asc'], 'desc' => $sortable['class_desc'])
		);
	}
	
	private function resolveFilterOptions()
	{
		if($this->tableType instanceof Type\FilterableInterface === false)
		{
			$this->filter = null;
			return;
		}
		
		// Set button option default values.
		$filterOptionsResolver = new OptionsResolver();
		$filterOptionsResolver->setDefaults(array(
			'submit' => array(),
			'reset' => array()
		));
		$this->tableType->setFilterButtonOptions($filterOptionsResolver);
		
		// Resolve the filter button options.
		$filter = $filterOptionsResolver->resolve(array());
		
		// Set submit button default values.
		$submitOptionsResolver = new OptionsResolver();
		$submitOptionsResolver->setDefaults(array(
			'label' => 'submit',
			'class' => array()
		));
		
		// Resolve submit button options.
		$submit = $submitOptionsResolver->resolve($filter['submit']);
		
		// Set submit button default values.
		$resetOptionsResolver = new OptionsResolver();
		$resetOptionsResolver->setDefaults(array(
			'label' => 'reset',
			'class' => array()
		));
		
		// Resolve submit button options.
		$reset = $submitOptionsResolver->resolve($filter['reset']);
		
		// Set up the options container.
		$this->filter = new FilterOptionsContainer($submit['label'], $submit['class'], $reset['label'], $reset['class']);
		
		// Sets value of all filters.
		foreach($this->getFilters() as $filter)
		{
			/* @var $filter FilterInterface */
			
			$filterValue = (string) $this->request->query->get($filter->getName(), '');

			$filter->setValue(trim($filterValue));
		}
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
}
