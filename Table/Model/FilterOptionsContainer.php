<?php

namespace JGM\TableBundle\Table\Model;

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
	protected $submitAttributes;
	
	/**
	 * @var string
	 */
	protected $resetLabel;
	
	/**
	 * @var array
	 */
	protected $resetAttributes;
	
	public function __construct($submitLabel, array $submitAttributes, $resetLabel, array $resetAttributes)
	{
		$this->submitLabel = $submitLabel;
		$this->submitAttributes = $submitAttributes;
		$this->resetLabel = $resetLabel;
		$this->resetAttributes = $resetAttributes;
	}
	
	public function getSubmitLabel()
	{
		return $this->submitLabel;
	}

	public function getSubmitAttributes()
	{
		return $this->submitAttributes;
	}

	public function getResetLabel()
	{
		return $this->resetLabel;
	}

	public function getResetAttributes()
	{
		return $this->resetAttributes;
	}
}
