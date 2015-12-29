<?php

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
	const EVENT_CREATE = 'creating';
	const EVENT_BUILD_VIEW = 'build-view';
	const EVENT_FETCH_DATA = 'fetch-data';
	const EVENT_RENDER_TABLE = 'render-table';
	const EVENT_RENDER_PAGINATION = 'render-pagination';
	const EVENT_RENDER_FILTER = 'render-table';
	
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
	
	public function __construct($isDebug)
	{
		$this->isDebug = (boolean) $isDebug;
		$this->stopwatches = array();
		$this->events = array();
	}
	
	/**
	 * Starts the stopwatch for an event of a table.
	 * 
	 * @param string $tableName	Name of the table.
	 * @param string $event		Name of the event.
	 */
	public function start($tableName, $event)
	{
		if($this->isDebug === true)
		{
			if(array_key_exists($tableName, $this->stopwatches) === false)
			{
				$this->stopwatches[$tableName] = new Stopwatch();
			}
			
			$this->stopwatches[$tableName]->start($event);
		}
	}
	
	/**
	 * Stops the table for an event of a table.
	 * 
	 * @param string $tableName	Name of the table.
	 * @param string $event		Name of the event.
	 */
	public function stop($tableName, $event)
	{
		if($this->isDebug === true)
		{
			$stopwatch = $this->getStopwatch($tableName);
			/* @var $stopwatch Stopwatch */
			
			if($stopwatch !== null && $stopwatch->isStarted($event))
			{
				if(array_key_exists($tableName, $this->events) === false)
				{
					$this->events[$tableName] = array();
				}
				
				$this->events[$tableName][$event] = $stopwatch->stop($event);
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
	
	/**
	 * Calcualtes the duration for all stopped stopwatches.
	 * 
	 * @return int
	 */
	public function getDuration()
	{
		$duration = 0;
		foreach($this->events as $events)
		{
			foreach($events as $event)
			{
				/* @var $event StopwatchEvent */
				$duration += $event->getDuration();
			}
		}
		
		return $duration;
	}
	
	/**
	 * Calcualtes the memory for all stopped stopwatches.
	 * 
	 * @return int
	 */
	public function getMemory()
	{
		$memory = 0;
		foreach($this->events as $events)
		{
			foreach($events as $event)
			{
				/* @var $event StopwatchEvent */
				$memory += $event->getMemory();
			}
		}
		
		return $memory;
	}
}
