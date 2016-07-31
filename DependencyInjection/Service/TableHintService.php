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

/**
 * Service for collecting hints during the 
 * building process. Hints are displayed
 * at the profiler in debug mode.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class TableHintService
{
	/**
	 * Is the application running in
	 * debug mode?
	 * 
	 * @var boolean
	 */
	private $isDebug;
	
	/**
	 * List of hints.
	 * 
	 * @var array
	 */
	private $hints;
	
	public function __construct($isDebug)
	{
		$this->isDebug = $isDebug;
		$this->hints = array();
	}
	
	public function getHints()
	{
		return $this->hints;
	}
	
	public function addHint($tableName, $hint)
	{
		if($this->isDebug === false)
		{
			return;
		}
		
		$this->hints[] = array(
			'table' => $tableName,
			'message' => $hint
		);
	}
}
