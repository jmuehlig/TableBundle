<?php

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
	
	public function __construct($paramDirectionName, $paramColumnName, $emptyDirection, $emptyColumn, array $classes)
	{
		$this->paramDirectionName = $paramDirectionName;
		$this->paramColumnName = $paramColumnName;
		$this->emptyDirection = $emptyDirection;
		$this->emptyColumnName = $emptyColumn;
		$this->classes = $classes;
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
