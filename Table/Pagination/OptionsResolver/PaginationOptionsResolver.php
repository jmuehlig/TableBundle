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
use Symfony\Component\DependencyInjection\ContainerInterface;
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
	function __construct(ContainerInterface $container) 
	{
		$globalDefaults = $container->getParameter('jgm_table.pagination_default_options');
		
		$this->setDefaults(array(
			'param' => $globalDefaults['param'],
			'rows_per_page' => $globalDefaults['rows_per_page'],
			'show_empty' => $globalDefaults['show_empty'],
			'ul_class' => $globalDefaults['ul_class'],
			'li_class' => $globalDefaults['li_class'],
			'li_class_active' => $globalDefaults['li_class_active'],
			'li_class_disabled' => $globalDefaults['li_class_disabled'],
			'prev_label' => $globalDefaults['prev_label'],
			'next_label' => $globalDefaults['next_label'],
			'max_pages' => $globalDefaults['max_pages']
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
