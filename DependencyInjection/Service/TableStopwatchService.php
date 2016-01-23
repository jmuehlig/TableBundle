<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\DependencyInjection\Service;

use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

/**
 * StopwatchService for profiling tables in developer mode.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.2
 */
class TableStopwatchService
{
	const CATEGORY_CREATE = 'create';
	const CATEGORY_BUILD_VIEW = 'build-view';
	const CATEGORY_RENDER_TABLE = 'render-table';
	const CATEGORY_RENDER_FILTER = 'render-filter';
	
	/**
	 * @var boolean
	 */
	protected $isDebug;
	
	/**
	 * @var array
	 */
	protected $stopwatches;
	
	/**
	 * @var array
	 */
	protected $events;

	/**
	 * @var array
	 */
	protected $durations;
	
	public function __construct($isDebug)
	{
		$this->isDebug = (boolean) $isDebug;
		$this->stopwatches = array();
		$this->events = array();
	}
	
	/**
	 * Starts the stopwatch for a category of a tables build state.
	 * 
	 * @param string $tableName	Name of the table.
	 * @param string $category	Name of the category.
	 */
	public function start($tableName, $category)
	{
		if($this->isDebug === true)
		{
			if(array_key_exists($tableName, $this->stopwatches) === false)
			{
				$this->stopwatches[$tableName] = new Stopwatch();
			}
			
			$this->stopwatches[$tableName]->start($category);
		}
	}
	
	/**
	 * Stops the table for a category of a tables build state.
	 * 
	 * @param string $tableName	Name of the table.
	 * @param string $category	Name of the category.
	 */
	public function stop($tableName, $category)
	{
		if($this->isDebug === true)
		{
			$stopwatch = $this->getStopwatch($tableName);
			/* @var $stopwatch Stopwatch */
			
			if($stopwatch !== null && $stopwatch->isStarted($category))
			{
				if(array_key_exists($tableName, $this->events) === false)
				{
					$this->events[$tableName] = array();
				}
				
				if(array_key_exists($category, $this->events[$tableName]) === false)
				{
					$this->events[$tableName][$category] = array();
				}
				
				$this->events[$tableName][$category][] = $stopwatch->stop($category);
			}
		}
	}
	
	/**
	 * Get the stopwatch for one table.
	 * 
	 * @param string $tableName
	 * @return Stopwatch
	 */
	protected function getStopwatch($tableName)
	{
		if(array_key_exists($tableName, $this->stopwatches) === false)
		{
			return null;
		}
		
		return $this->stopwatches[$tableName];
	}
	
	public function getDuration($tableName = null, $category = null)
	{
		$events = array();
		if($tableName !== null)
		{
			if($category !== null)
			{
				if(array_key_exists($category, $this->events[$tableName]))
				{
					$events = $this->events[$tableName][$category];
				}
			}
			else
			{
				$events = array();
				foreach($this->events[$tableName] as $categoryEvents)
				{
					$events = array_merge($events, $categoryEvents);
				}
			}
		}
		else
		{
			$events = array();
			foreach($this->events as $categories)
			{
				foreach($categories as $categoryEvents)
				{
					$events = array_merge($events, $categoryEvents);
				}
			}
		}
		
		$duration = 0.0;
		foreach($events as $event)
		{
			/* @var $event StopwatchEvent */
			$duration += $event->getDuration();
		}
		
		return $duration;
	}
	
	/**
	 * Returns the amount of tables.
	 * 
	 * @return int
	 */
	public function getCountTables()
	{
		return count($this->events);
	}
	
	public function getStoppedTables()
	{
		return array_keys($this->events);
	}
	
	public function getStopwatchesData()
	{
		$data = array();
		foreach($this->getStoppedTables() as $tableName)
		{
			$tableData = array();
			$tableData[] = $this->getDuration($tableName, self::CATEGORY_CREATE);
			$tableData[] = $this->getDuration($tableName, self::CATEGORY_BUILD_VIEW);
			$tableData[] = $this->getDuration($tableName, self::CATEGORY_RENDER_TABLE);
			$tableData[] = $this->getDuration($tableName, self::CATEGORY_RENDER_FILTER);
			$tableData[] = $tableData[0] + $tableData[1] + $tableData[2] + $tableData[3];
			
			$data[$tableName] = $tableData;
		}

		return $data;
	}
}
