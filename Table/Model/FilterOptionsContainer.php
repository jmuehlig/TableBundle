<?php

namespace PZAD\TableBundle\Table\Model;

/**
 * Container for options of the filter component.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0.0
 */
class FilterOptionsContainer
{
	/**
	 * @var string
	 */
	protected $submitLabel;
	
	/**
	 * @var array
	 */
	protected $submitClasses;
	
	/**
	 * @var string
	 */
	protected $resetLabel;
	
	/**
	 * @var array
	 */
	protected $resetClasses;
	
	public function __construct($submitLabel, array $submitClasses, $resetLabel, array $resetClasses)
	{
		$this->submitLabel = $submitLabel;
		$this->submitClasses = $submitClasses;
		$this->resetLabel = $resetLabel;
		$this->resetClasses = $resetClasses;
	}
	
	public function getSubmitLabel()
	{
		return $this->submitLabel;
	}

	public function getSubmitClasses()
	{
		return $this->submitClasses;
	}

	public function getResetLabel()
	{
		return $this->resetLabel;
	}

	public function getResetClasses()
	{
		return $this->resetClasses;
	}
}
