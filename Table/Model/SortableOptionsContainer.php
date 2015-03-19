<?php

namespace JGM\TableBundle\Table\Model;

/**
 * Container for the options of the sortable component.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0.0
 */
class SortableOptionsContainer
{
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
	protected $direction;
	
	/**
	 * @var string
	 */
	protected $columnName;
	
	/**
	 * @var array
	 */
	protected $classes;
	
	public function __construct($paramDirectionName, $paramColumnName, $direction, $column, array $classes)
	{
		$this->paramDirectionName = $paramDirectionName;
		$this->paramColumnName = $paramColumnName;
		$this->direction = $direction;
		$this->columnName = $column;
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

	public function getDirection()
	{
		return $this->direction;
	}

	public function getColumnName()
	{
		return $this->columnName;
	}

	public function getClasses()
	{
		return $this->classes;
	}
}
