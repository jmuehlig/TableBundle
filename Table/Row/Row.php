<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Row;

use JGM\TableBundle\Table\Utils\ReflectionHelper;

/**
 * Represents a row of tabla data.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class Row
{	
	/**
	 * Object.
	 * 
	 * @var object
	 */
	private $entity;
	
	/**
	 * Ongoing row number.
	 * 
	 * @var int 
	 */
	private $count;
	
	/**
	 * Attributes of the row.
	 * 
	 * @var array
	 */
	private $attributes;
	
	/**
	 * True, if this row is selected
	 * by the user.
	 * 
	 * @var boolean
	 */
	private $isSelected;
	
	public function __construct($entity, $count, $isSelected = false)
	{
		$this->entity = $entity;
		$this->count = $count;
		$this->isSelected = $isSelected;
		$this->attributes = array();
	}
	
	public function getEntity()
	{
		return $this->entity;
	}
	
	public function get($property)
	{
		return ReflectionHelper::getPropertyOfEntity($this->getEntity(), $property);
	}
	
	/**
	 * Get the ongoing number of rows for this row.
	 * 
	 * @return int
	 */
	public function getCount()
	{
		return $this->count;
	}
	
	public function setAttributes(array $attributes)
	{
		$this->attributes = $attributes;
	}
	
	/**
	 * @return array Attributes of this row.
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}
	
	/**
	 * True, if this row is selected
	 * by the user.
	 * 
	 * @since	1.3
	 * @return	boolean
	 */
	public function isSelected()
	{
		return $this->isSelected;
	}
}
