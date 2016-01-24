<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Order\Model;

/**
 * Container for the options of the order component.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class Order
{
	const DIRECTION_ASC = 'asc';
	const DIRECTION_DESC = 'desc';
	
	/**
	 * @var string
	 */
	protected $template;
	
	/**
	 * @var string
	 */
	protected $paramDirectionName;
	
	/**
	 * @var string 
	 */
	protected $paramColumnName;
	
	/**
	 * @var string
	 */
	protected $emptyDirection = self::DIRECTION_ASC;
	
	/**
	 * @var string
	 */
	protected $emptyColumnName;
	
	/**
	 * @var string
	 */
	protected $currentDirection;
	
	/**
	 * @var string
	 */
	protected $currentColumnName;
	
	/**
	 * @var array
	 */
	protected $classes;
	
	public function __construct($template, $paramDirectionName, $paramColumnName, $emptyDirection, $emptyColumn, array $classes)
	{
		$this->template = $template;
		$this->paramDirectionName = $paramDirectionName;
		$this->paramColumnName = $paramColumnName;
		$this->emptyDirection = $emptyDirection;
		$this->emptyColumnName = $emptyColumn;
		$this->classes = $classes;
	}
	
	public function getTemplate()
	{
		return $this->template;
	}
	
	public function getParamDirectionName()
	{
		return $this->paramDirectionName;
	}

	public function getParamColumnName()
	{
		return $this->paramColumnName;
	}
	
	public function getEmptyDirection() 
	{
		return $this->emptyDirection;
	}
	
	public function getEmptyColumnName()
	{
		return $this->emptyColumnName;
	}

	public function getClasses()
	{
		return $this->classes;
	}
	
	public function getCurrentDirection()
	{
		return $this->currentDirection;
	}

	public function getCurrentColumnName()
	{
		return $this->currentColumnName;
	}
	
	public function setCurrentDirection($currentDirection) 
	{
		$this->currentDirection = $currentDirection;
	}

	public function setCurrentColumnName($currentColumnName) 
	{
		$this->currentColumnName = $currentColumnName;
	}

	public function setParamDirectionName($name)
	{
		$this->paramDirectionName = $name;
	}

	public function setParamColumnName($name)
	{
		$this->paramColumnName = $name;
	}
}
