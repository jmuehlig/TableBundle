<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Pagination\OptionsResolver;

use JGM\TableBundle\Table\Pagination\Model\Pagination;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Default options resolver for the pagination component.
 * Setting the default pagination options and transforms
 * the options to the Pagination model.
 *
 * @author	Jan Mühlig
 * @since	1.0
 */
class PaginationOptionsResolver extends OptionsResolver
{
	function __construct() 
	{
		$this->setDefaults(array(
			'param' => 'page',
			'rows_per_page' => 20,
			'show_empty' => true,
			'ul_class' => 'pagination',
			'li_class' => null,
			'li_class_active' => 'active',
			'li_class_disabled' => 'disabled',
			'prev_label' => '&laquo;',
			'next_label' => '&raquo;',
			'max_pages' => null
		));
	}
	
	/**
	 * Creating a pagination model from
	 * resolver.
	 * 
	 * @return Pagination
	 */
	public function toPagination()
	{
		$pagination = $this->resolve(array());
		
		$classes = array();
		$classes['ul'] = $pagination['ul_class'];
		$classes['li'] = array();
		$classes['li']['default'] = array();
		$classes['li']['active'] = array();
		$classes['li']['disabled'] = array();
		
		if($pagination['li_class'] !== null && !empty($pagination['li_class']))
		{
			$classes['li']['default'][] = $pagination['li_class'];
			$classes['li']['active'][] = $pagination['li_class'];
			$classes['li']['disabled'][] = $pagination['li_class'];
		}
		
		if($pagination['li_class_active'] !== null && !empty($pagination['li_class_active']))
		{
			$classes['li']['active'][] = $pagination['li_class_active'];
		}
		
		if($pagination['li_class_disabled'] !== null && !empty($pagination['li_class_disabled']))
		{
			$classes['li']['disabled'][] = $pagination['li_class_disabled'];
		}
		
		return new Pagination(
			$pagination['param'],
			$pagination['rows_per_page'],
			$pagination['show_empty'],
			$classes,
			$pagination['prev_label'],
			$pagination['next_label'],
			$pagination['max_pages']
		);
	}
}
