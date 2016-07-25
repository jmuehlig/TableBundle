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

use JGM\TableBundle\Table\Filter\OptionsResolver\FilterOptions;

/**
 * Container for options of the filter component.
 *
 * @author		Jan Mühlig <mail@janmuehlig.de>
 * @since		1.0
 * @deprecated	since version 1.3
 */
class Filter
{
	/**
	 * @var arary
	 */
	private $options;
	
	public function __construct(array $options)
	{
		$this->options = $options;
	}
	
	public function getTemplate()
	{
		return $this->options[FilterOptions::TEMPLATE];
	}
	
	public function getSubmitLabel()
	{
		return $this->options[FilterOptions::SUBMIT_LABEL];
	}

	public function getSubmitAttributes()
	{
		return $this->options[FilterOptions::SUBMIT_ATTRIBUTES];
	}

	public function getResetLabel()
	{
		return $this->options[FilterOptions::RESET_LABEL];
	}

	public function getResetAttributes()
	{
		return $this->options[FilterOptions::RESET_ATTRIBUTES];
	}
}
