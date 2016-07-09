<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
	protected $template;
	
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
	
	/**
	 *
	 * @var array
	 */
	protected $optionValues;
	
	/**
	 * @var array
	 */
	protected $optionAttributes;
	
	/**
	 * @var string
	 */
	protected $optionLabel;
	
	/**
	 * @var array
	 */
	protected $optionLabelAttributes;
	
	/**
	 * @var string
	 */
	protected $optionSubmitLabel;
	
	/**
	 * @var array
	 */
	protected $optionSubmitAttributes;
	
	public function __construct($template, $parameterName, $itemPerRow, $showEmpty,
								array $classes, $previousLabel, $nextLabel, $maxPages,
								array $optionValues, array $optionAttributes, $optionLabel,
								array $optionLabelAttributes, $optionSubmitLabel, array $optionSubmitAttributes)
	{
		$this->template = $template;
		$this->parameterName = $parameterName;
		$this->itemsPerRow = $itemPerRow;
		$this->showEmpty = $showEmpty;
		$this->classes = $classes;
		$this->previousLabel = $previousLabel;
		$this->nextLabel = $nextLabel;
		$this->maxPages = $maxPages;
		
		$this->optionValues = $optionValues;
		$this->optionAttributes = $optionAttributes;
		$this->optionLabel = $optionLabel;
		$this->optionLabelAttributes = $optionLabelAttributes;
		$this->optionSubmitLabel = $optionSubmitLabel;
		$this->optionSubmitAttributes = $optionSubmitAttributes;
	}
	
	public function getTemplate()
	{
		return $this->template;
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
	
	public function getOptionValues()
	{
		return $this->optionValues;
	}

	public function getOptionAttributes()
	{
		return $this->optionAttributes;
	}

	public function getOptionLabel()
	{
		return $this->optionLabel;
	}

	public function getOptionLabelAttributes()
	{
		return $this->optionLabelAttributes;
	}
	
	public function getOptionSubmitLabel()
	{
		return $this->optionSubmitLabel;
	}

	public function getOptionSubmitAttributes()
	{
		return $this->optionSubmitAttributes;
	}

	public function setCurrentPage($currentPage)
	{
		$this->currentPage = $currentPage;
	}
	
	public function setParameterName($parameterName)
	{
		$this->parameterName = $parameterName;
	}
	
	public function setItemsPerPage($itemsPerPage)
	{
		$this->itemsPerRow = $itemsPerPage;
	}
}
