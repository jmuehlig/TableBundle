<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\DataSource;

use DateTime;
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
	
	/**
	 * Array with filter functions.
	 * 
	 * @var array
	 */
	protected $filterFunctions;
	
	public function __construct(array $data)
	{
		$this->data = $data;
		
		$this->filterFunctions = array(
			FilterOperator::EQ			=> function($item, $filter) { return $item == $filter; },
			FilterOperator::NOT_EQ		=> function($item, $filter) { return $item != $filter; },
			FilterOperator::GEQ			=> function($item, $filter) { return $item >= $filter; },
			FilterOperator::GT			=> function($item, $filter) { return $item > $filter; },
			FilterOperator::LEQ			=> function($item, $filter) { return $item <= $filter; },
			FilterOperator::LT			=> function($item, $filter) { return $item < $filter; },
			FilterOperator::LIKE		=> function($item, $filter) { return strpos(strtolower($item), strtolower($filter)) !== false; },
			FilterOperator::NOT_LIKE	=> function($item, $filter) { return strpos(strtolower($item), strtolower($filter)) === false; },
		);
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
			$data = array_filter($this->data, function($row)use($filters) {
				return $this->survivesFilters($row, $filters);
			});
			
			return count( $this->cache($data, $columns, $filters) );
		}
		
		return count($data);
	}
	
	public function getData(ContainerInterface $container, array $columns, array $filters = null, Pagination $pagination = null, Order $sortable = null)
	{
		$container->get('jgm.table_hint')->addHint(
			$container->get('jgm.table_context')->getCurrentTableName(),
			'The used ArrayDataSource may be slower than other data sources, espaccially on filtering.'
		);
		
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
				$tmpData = array_filter($this->data, function($row) use($filters) {
					return $this->survivesFilters($row, $filters);
				});

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
			return array_slice(
				$data, 
				$pagination->getCurrentPage() * $pagination->getItemsPerRow(), 
				$pagination->getItemsPerRow()
			);
		}
		
		return $data;
	} 
	
	public function getType()
	{
		return 'array';
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
				
				if($filter->getValue() instanceof DateTime)
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
		$activeFilters = array_filter($filters, function($filter) {
			return $filter->isActive();
		});
		foreach($activeFilters as $filter)
		{
			/* @var $filter FilterInterface */
			
			$surviveFilter = false;
			foreach($filter->getColumns() as $column)
			{
				$itemValue = ReflectionHelper::getPropertyOfEntity($item, $column);
				if($itemValue === null)
				{
					$surviveFilter = $surviveFilter || false;
					continue;
				}
				
				if($filter instanceof EntityFilter)
				{
					$itemValue = ReflectionHelper::getPropertyOfEntity($itemValue, 'id');
				}
				
				$surviveFilter = $surviveFilter || call_user_func(
					$this->filterFunctions[$filter->getOperator()],
					$itemValue, 
					$filter->getValue()
				);
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
