<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JGM\TableBundle\DependencyInjection\Service;

/**
 * Description of TableHintService
 *
 * @author Jan
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
