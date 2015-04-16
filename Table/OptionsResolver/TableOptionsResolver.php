<?php

namespace JGM\TableBundle\Table\OptionsResolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * OptionsResolver for table options, used to resolve
 * options, set at the table type.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class TableOptionsResolver extends OptionsResolver
{
	function __construct() 
	{
		$this->setDefaults(array(
			'empty_value' => 'No data found.',
			'attr' => array(),
			'head_attr' => array(),
		));
		
		$this->setAllowedTypes(array(
			'attr' => 'array',
			'head_attr' => 'array'
		));
	}
}
