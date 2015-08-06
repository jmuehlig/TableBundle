<?php

namespace JGM\TableBundle\Table\DataSource;

use JGM\TableBundle\Table\Filter\DateFilter;
use JGM\TableBundle\Table\Filter\EntityFilter;
use JGM\TableBundle\Table\Filter\FilterInterface;
use JGM\TableBundle\Table\Filter\FilterOperator;
use JGM\TableBundle\Table\Order\Model\Order;
use JGM\TableBundle\Table\Pagination\Model\Pagination;
use JGM\TableBundle\Table\Utils\ReflectionHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * DataSource, which deals arrays.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class ArrayDataSource implements DataSourceInterface
{
	/**
	 * Cached data.
	 * 
	 * @var array
	 */
	protected $cachedData = null;
	
	/**
	 * Hash for the cached data.
	 * 
	 * @var string
	 */
	protected $cacheHash = null;
	
	/**
	 * The data.
	 * 
	 * @var array
	 */
	protected $data;
	
	public function __construct(array $data)
	{
		$this->data = $data;
	}
	
	public function getCountItems(ContainerInterface $container, array $columns, array $filters = null)
	{
		if($this->cachedData !== null && $this->cacheHash === $this->generateHash($columns, $filters))
		{
			return count($this->cachedData);
		}
		
		$this->clearCache();
		
		if($filters !== null)
		{
			$data = array();
			foreach($this->data as $row)
			{
				if($this->survivesFilters($row, $filters) === true)
				{
					$data[] = $row;
				}
			}
			
			return count( $this->cache($data, $columns, $filters) );
		}
		
		return count($data);
	}
	
	public function getData(ContainerInterface $container, array $columns, array $filters = null, Pagination $pagination = null, Order $sortable = null)
	{
		// Get the filtered data.
		if($this->cachedData !== null && $this->cacheHash === $this->generateHash($columns, $filters))
		{
			$data = $this->cachedData;
		}
		else
		{
			$this->clearCache();

			if($filters !== null)
			{
				$tmpData = array();
				foreach($this->data as $row)
				{
					if($this->survivesFilters($row, $filters) === true)
					{
						$tmpData[] = $row;
					}
				}

				$data = $this->cache($tmpData, $columns, $filters);
			}
		}
		
		// Sort the data.
		if($sortable !== null)
		{
			usort($data, $this->getSortFunction($sortable));
		}
		
		// Paginate.
		if($pagination !== null)
		{
			$start = $pagination->getCurrentPage() * $pagination->getItemsPerRow();
			$end = min( array($start + $pagination->getItemsPerRow(), count($data)) );
			$result = [];
			for($i = $start; $i < $end; $i++)
			{
				$result[] = $data[$i];
			}
		
			return $result;
		}
		
		return $data;
	} 
	
	public function getType()
	{
		return 'collection';
	}
	
	/**
	 * Generates a hash value, which is used
	 * to cache the data between count and getter.
	 * 
	 * @param array $columns	Columns.
	 * @param array $filters	Filter (optional).
	 */
	protected function generateHash(array $columns, array $filters = null)
	{
		$value = "";
		foreach($columns as $key => $column)
		{
			$value += sprintf("%s:%s;", $key, get_class($column));
		}
		
		if($filters !== null && is_array($filters))
		{
			foreach($filters as $key => $filter)
			{
				/* @var $filter FilterInterface */
				
				if($filter->getValue() === null || $filter->getValue() === "")
				{
					continue;
				}
				
				if($filter instanceof DateFilter)
				{
					$value += sprintf("%s:%s;", $key, $filter->getValue()->getTimestamp());
				}
				else
				{
					$value += sprintf("%s:%s;", $key, $filter->getValue());
				}
			}
		}
		
		return md5($value);
	}
	
	/**
	 * Clears the cache.
	 */
	protected function clearCache()
	{
		$this->cachedData = array();
		$this->cacheHash = null;
	}
	
	/**
	 * Caches the data.
	 * 
	 * @param array $data		Data, that should be cached.
	 * @param array $columns	Columns.
	 * @param array $filters	Filters (optional).
	 */
	protected function cache(array $data, array $columns, array $filters = null)
	{
		$this->cachedData = $data;
		$this->cacheHash = $this->generateHash($columns, $filters);
		
		return $this->cachedData;
	}
	
	/**
	 * Applys the given filters to an data item.
	 * 
	 * @param mixed $item		Data item.
	 * @param array $filters	Filters.
	 * 
	 * @return boolean			True, if the data item is filtered by the given filters.
	 */
	protected function survivesFilters($item, array $filters)
	{
		foreach($filters as $filter)
		{
			/* @var $filter FilterInterface */
			
			$filterValue = $filter->getValue();
			if($filterValue === null || $filterValue === "")
			{
				continue;
			}
			
			$surviveFilter = false;
			foreach($filter->getColumns() as $column)
			{
				$itemValue = ReflectionHelper::getPropertyOfEntity($item, $column);
				if($filter instanceof EntityFilter)
				{
					$itemValue = ReflectionHelper::getPropertyOfEntity($itemValue, 'id');
				}
				
				if($filter->getOperator() === FilterOperator::EQ)
				{
					$surviveFilter = $surviveFilter || $itemValue == $filterValue;
				}
				else if($filter->getOperator() === FilterOperator::NOT_EQ)
				{
					$surviveFilter = $surviveFilter || $itemValue != $filterValue;
				}
				else if($filter->getOperator() === FilterOperator::GEQ)
				{
					$surviveFilter = $surviveFilter || $itemValue >= $filterValue;
				}
				else if($filter->getOperator() === FilterOperator::GT)
				{
					$surviveFilter = $surviveFilter || $itemValue > $filterValue;
				}
				else if($filter->getOperator() === FilterOperator::LEQ)
				{
					$surviveFilter = $surviveFilter || $itemValue <= $filterValue;
				}
				else if($filter->getOperator() === FilterOperator::LT)
				{
					$surviveFilter = $surviveFilter || $itemValue < $filterValue;
				}
				else if($filter->getOperator() === FilterOperator::LIKE)
				{
					$surviveFilter = $surviveFilter || strpos($itemValue, $filterValue) !== false;
				}
				else if($filter->getOperator() === FilterOperator::NOT_LIKE)
				{
					$surviveFilter = $surviveFilter || strpos($itemValue, $filterValue) === false;
				}
			}
							
			if($surviveFilter === false)
			{
				return false;
			}
		}
		
		return true;
	}

	protected function getSortFunction(Order $order)
	{
		$direction = $order->getCurrentDirection();
		$column = $order->getCurrentColumnName();
		return function($a, $b) use ($direction, $column) {
			$aValue = ReflectionHelper::getPropertyOfEntity($a, $column);
			$bValue = ReflectionHelper::getPropertyOfEntity($b, $column);
			if($direction === Order::DIRECTION_DESC)
			{
				return $aValue > $bValue ? -1 : 1;
			}
			else
			{
				return $aValue > $bValue ? 1 : -1;
			}
		};
	}
}
