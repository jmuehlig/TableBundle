<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Filter\Model;

/**
 * Container for options of the filter component.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class Filter
{
	/**
	 * @var string
	 */
	protected $template;
	
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
	
	public function __construct($template, $submitLabel, array $submitAttributes, $resetLabel, array $resetAttributes)
	{
		$this->template = $template;
		$this->submitLabel = $submitLabel;
		$this->submitAttributes = $submitAttributes;
		$this->resetLabel = $resetLabel;
		$this->resetAttributes = $resetAttributes;
	}
	
	public function getTemplate()
	{
		return $this->template;
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
