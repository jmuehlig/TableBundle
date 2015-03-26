<?php
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
		parent::__construct();
		
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
		return new Pagination(
			$pagination['param'],
			$pagination['rows_per_page'],
			$pagination['show_empty'],
			array(
				'ul' => $pagination['ul_class'],
				'li' => $pagination['li_class'],
				'li_active' => $pagination['li_class_active'],
				'li_disabled' => $pagination['li_class_disabled']
			),
			$pagination['prev_label'],
			$pagination['next_label'],
			$pagination['max_pages']
		);
	}
}
