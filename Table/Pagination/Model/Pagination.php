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

use JGM\TableBundle\Table\Pagination\OptionsResolver\PaginationOptions;

/**
 * Model for pagination information.
 *
 * @author		Jan Mühlig <mail@janmuehlig.de>
 * @since		1.0
 * @deprecated	since 1.3, will be removed at 1.5
 */
class Pagination 
{
	/**
	 * @var array
	 */
	private $options;
	
	public function __construct(array $options)
	{
		$this->options = $options;
	}
	
	public function getTemplate()
	{
		return $this->options[PaginationOptions::TEMPLATE];
	}
	
	public function getParameterName()
	{
		return $this->options[PaginationOptions::PARAM];
	}

	public function getItemsPerRow()
	{
		return $this->options[PaginationOptions::ROWS_PER_PAGE];
	}

	public function getCurrentPage()
	{
		return $this->options[PaginationOptions::CURRENT_PAGE];
	}
	
	public function getShowEmpty()
	{
		return $this->options[PaginationOptions::SHOW_EMPTY];
	}
	
	public function getClasses()
	{
		return array(
			'ul' => $this->options[PaginationOptions::UL_CLASS],
			'li' => array(
				'default' => $this->options[PaginationOptions::LI_CLASS],
				'active' => $this->options[PaginationOptions::LI_CLASS_ACTIVE],
				'disabled' => $this->options[PaginationOptions::LI_CLASS_DISABLED]
			)
		);
	}
	
	public function getPreviousLabel()
	{
		return $this->options[PaginationOptions::PREV_LABEL];
	}

	public function getNextLabel()
	{
		return $this->options[PaginationOptions::NEXT_LABEL];
	}
	
	public function getMaxPages()
	{
		return $this->options[PaginationOptions::MAX_PAGES];
	}
	
	public function getOptionValues()
	{
		return $this->options[PaginationOptions::OPTION_VALUES];
	}

	public function getOptionAttributes()
	{
		return $this->options[PaginationOptions::OPTION_ATTRIBUTES];
	}

	public function getOptionLabel()
	{
		return $this->options[PaginationOptions::OPTION_LABEL];
	}

	public function getOptionLabelAttributes()
	{
		return $this->options[PaginationOptions::OPTION_LABEL_ATTRIBUTES];
	}
	
	public function getOptionSubmitLabel()
	{
		return $this->options[PaginationOptions::OPTION_SUBMIT_LABEL];
	}

	public function getOptionSubmitAttributes()
	{
		return $this->options[PaginationOptions::OPTION_LABEL_ATTRIBUTES];
	}
}
