<?php

namespace JGM\TableBundle\Table\Pagination\Model;

/**
 * Model for pagination information.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class Pagination 
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
	
	/**
	 * @var string
	 */
	protected $previousLabel;
	
	/**
	 * @var string
	 */
	protected $nextLabel;
	
	/**
	 * @var int
	 */
	protected $maxPages;


	public function __construct($parameterName, $itemPerRow, $showEmpty, array $classes, 
								$previousLabel, $nextLabel, $maxPages)
	{
		$this->parameterName = $parameterName;
		$this->itemsPerRow = $itemPerRow;
		$this->showEmpty = $showEmpty;
		$this->classes = $classes;
		$this->previousLabel = $previousLabel;
		$this->nextLabel = $nextLabel;
		$this->maxPages = $maxPages;
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
	
	public function getPreviousLabel()
	{
		return $this->previousLabel;
	}

	public function getNextLabel()
	{
		return $this->nextLabel;
	}
	
	public function getMaxPages()
	{
		return $this->maxPages;
	}
	
	public function setCurrentPage($currentPage)
	{
		$this->currentPage = $currentPage;
	}
}
