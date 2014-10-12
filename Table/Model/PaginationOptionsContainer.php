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
	 * @var boolean
	 */
	protected $showEmpty;
	
	/**
	 * @var array
	 */
	protected $classes;


	public function __construct($parameterName, $itemPerRow, $currentPage, $showEmpty, array $classes)
	{
		$this->parameterName = $parameterName;
		$this->itemsPerRow = $itemPerRow;
		$this->currentPage = $currentPage;
		$this->showEmpty = $showEmpty;
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
	
	public function getShowEmpty()
	{
		return $this->showEmpty;
	}
	
	public function getClasses()
	{
		return $this->classes;
	}
}
