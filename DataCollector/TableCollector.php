<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JGM\TableBundle\DataCollector;

use Exception;
use JGM\TableBundle\DependencyInjection\Service\TableStopwatchService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Description of TableCollector
 *
 * @author Jan
 */
class TableCollector extends DataCollector
{
	/**
	 * @var TableStopwatchService
	 */
	private $stopwatchService;
	
	public function __construct(TableStopwatchService $stopwatchService)
	{
		$this->stopwatchService = $stopwatchService;
		$this->data = array();
	}
	
	public function collect(Request $request, Response $response, Exception $exception = null)
	{
		$this->data['count'] = $this->stopwatchService->getCountTables();
		$this->data['duration'] = $this->stopwatchService->getSumDuration();
		$this->data['memory'] = $this->stopwatchService->getSumMemory();
		$this->data['tables'] = $this->stopwatchService->getStopwatchesData();
	}
	
	public function getCount()
	{
		return $this->data['count'];
	}
	
	public function getDuration()
	{
		return $this->data['duration'];
	}
	
	public function getMemory()
	{
		return $this->data['memory'];
	}
	
	public function getStopwatches()
	{
		return $this->data['tables'];
	}

	public function getName()
	{
		return 'jgm.table_collector';
	}
}
