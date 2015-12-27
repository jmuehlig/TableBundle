<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\OptionsResolver;

use Symfony\Component\DependencyInjection\ContainerInterface;
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
	function __construct(ContainerInterface $container) 
	{
		$globalDefaults = $container->getParameter('jgm_table.default_options');
		
		$this->setDefaults(array(
			'empty_value' => $globalDefaults['empty_value'],
			'attr' => $globalDefaults['attr'],
			'head_attr' => $globalDefaults['head_attr'],
			'hide_empty_columns' => $globalDefaults['hide_empty_columns'],
			'use_filter' => $globalDefaults['use_filter'],
			'use_pagination' => $globalDefaults['use_pagination'],
			'use_order' => $globalDefaults['use_order']
		));
		
		$this->setAllowedTypes('attr', 'array');
		$this->setAllowedTypes('head_attr', 'array');
	}
}
