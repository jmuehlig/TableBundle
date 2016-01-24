<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Filter\OptionsResolver;

use JGM\TableBundle\Table\Filter\Model\Filter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * OptionsResolver for filter options, used to resolve
 * options, set at the filter table type.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class FilterOptionsResolver extends OptionsResolver
{
	function __construct(ContainerInterface $container) 
	{
		$globalDefaults = $container->getParameter('jgm_table.filter_default_options');
		
		$this->setDefaults(array(
			'template' => $globalDefaults['template'],
			'submit_label' => $globalDefaults['submit_label'],
			'reset_label' => $globalDefaults['reset_label'],
			'submit_attr' => $globalDefaults['submit_attr'],
			'reset_attr' => $globalDefaults['reset_attr']
		));
		
		$this->setAllowedTypes('template', 'string');
		$this->setAllowedTypes('submit_label', 'string');
		$this->setAllowedTypes('reset_label', 'string');
		$this->setAllowedTypes('submit_attr', 'array');
		$this->setAllowedTypes('reset_attr', 'array');
	}
	
	/**
	 * Creating a filter model from resolver.
	 * 
	 * @return Filter
	 */
	public function toFilter()
	{
		$options = $this->resolve(array());
		
		return new Filter(
			$options['template'],
			$options['submit_label'], 
			$options['submit_attr'],
			$options['reset_label'],
			$options['reset_attr']
		);
	}
}
