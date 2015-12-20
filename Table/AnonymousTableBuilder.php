<?php

namespace JGM\TableBundle\Table;

use Doctrine\ORM\QueryBuilder;
use JGM\TableBundle\Table\DataSource\ArrayDataSource;
use JGM\TableBundle\Table\DataSource\DataSourceInterface;
use JGM\TableBundle\Table\DataSource\EntityDataSource;
use JGM\TableBundle\Table\DataSource\QueryBuilderDataSource;
use JGM\TableBundle\Table\Filter\Type\FilterTypeInterface;
use JGM\TableBundle\Table\Order\Type\OrderTypeInterface;
use JGM\TableBundle\Table\Pagination\Type\PaginationTypeInterface;
use JGM\TableBundle\Table\Type\AbstractTableType;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of AnonymousTableBuilder
 *
 * @author Jan
 */
class AnonymousTableBuilder extends AbstractTableType implements PaginationTypeInterface, OrderTypeInterface, FilterTypeInterface
{
	/**
	 * @var string 
	 */
	protected $name;
	
	/**
	 * @var array
	 */
	protected $tableOptions;
	
	/**
	 * @var array
	 */
	protected $filterOptions;
	
	/**
	 * @var array
	 */
	protected $paginationOptions;
	
	/**
	 * @var array
	 */
	protected $orderOptions;
	
	/**
	 * @var Table
	 */
	protected $table;
	
	/**
	 * @var DataSourceInterface
	 */
	protected $dataSource;
	
	/**
	 * @var array
	 */
	protected $columns;
	
	/**
	 * @var array
	 */
	protected $filters;
	
	public function __construct($name, array $options, Table $table)
	{
		$this->name = $name;
		$this->tableOptions = $options;
		$this->table = $table;
		
		$this->columns = array();
		$this->filters = array();
		$this->filterOptions = array();
		$this->orderOptions = array();
		$this->paginationOptions = array();
	}
	
	public function addColumn($type, $name, array $options = array())
	{
		$this->columns[] = array($type, $name, $options);
		
		return $this;
	}
	
	public function forEntity($entity, $alias = 't', $callback = null)
	{
		$this->setDataSource(new EntityDataSource($entity, $alias, $callback));
		
		return $this;
	}
	
	public function forQuery(QueryBuilder $queryBuilder)
	{
		$this->setDataSource(new QueryBuilderDataSource($queryBuilder));
		
		return $this;
	}
	
	public function forArray(array $data)
	{
		$this->setDataSource(new ArrayDataSource($data));
		
		return $this;
	}
	
	public function setDataSource(DataSourceInterface $dataSource)
	{
		$this->dataSource = $dataSource;
		
		return $this;
	}
	
	public function enablePagination(array $options = array())
	{
		$this->tableOptions['use_pagination'] = true;
		$this->paginationOptions = $options;
		
		return $this;
	}
	
	public function enableOrder(array $options = array())
	{
		$this->tableOptions['use_order'] = true;
		$this->orderOptions = $options;
		
		return $this;
	}
	
	public function enableFilter(array $options = array())
	{
		$this->tableOptions['use_filter'] = true;
		$this->filterOptions = $options;
		
		return $this;
	}
	
	public function addFilter($type, $name, array $options = array())
	{
		$this->filters[] = array($type, $name, $options);
		
		return $this;
	}
	
	public function getTable()
	{
		return $this->table->create($this, $this->options);
	}

	public function getDataSource(ContainerInterface $container)
	{
		return $this->dataSource;
	}

	public function getName()
	{
		return $this->name;
	}
	
	public function buildTable(TableBuilder $builder)
	{
		foreach($this->columns as $column)
		{
			$builder->add($column[0], $column[1], $column[2]);
		}
	}

	public function buildFilter(Filter\FilterBuilder $filterBuilder)
	{
		foreach($this->filters as $filter)
		{
			$filterBuilder->add($filter[0], $filter[1], $filter[2]);
		}
	}

	public function configureFilterButtonOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
	{
		$resolver->setDefaults($this->filterOptions);
	}

	public function configureOrderOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
	{
		$resolver->setDefaults($this->orderOptions);
	}

	public function configurePaginationOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
	{
		$resolver->setDefaults($this->paginationOptions);
	}

}
