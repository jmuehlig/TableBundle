<?php

namespace PZAD\TableBundle\Table\Model;

/**
 * Container for options of the pagination component.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0.0
 */
class PaginationOptionsContainer
{
	/**
	 * @var string
	 */
	protected $parameterName;
	
	/**
	 * @var int 
	 */
	protected $itemsPerRow;
	
	/**
	 * @var int
	 */
	protected $currentPage;
	
	/**
	 * @var array
	 */
	protected $classes;


	public function __construct($parameterName, $itemPerRow, $currentPage, array $classes)
	{
		$this->parameterName = $parameterName;
		$this->itemsPerRow = $itemPerRow;
		$this->currentPage = $currentPage;
		$this->classes = $classes;
	}
	
	public function getParameterName()
	{
		return $this->parameterName;
	}

	public function getItemsPerRow()
	{
		return $this->itemsPerRow;
	}

	public function getCurrentPage()
	{
		return $this->currentPage;
	}
	
	public function getClasses()
	{
		return $this->classes;
	}
}
