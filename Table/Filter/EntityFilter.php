<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PZAD\TableBundle\Table\Filter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of EntityFilter
 *
 * @author Jan
 */
class EntityFilter extends AbstractFilter
{
	protected function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		$optionsResolver->setAllowedValues(array(
			'type' => array('select', 'list')
		));
		
		parent::setDefaultFilterOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'type' => 'select'
		));
	}


	public function needsFormEnviroment()
	{
		
	}

	public function render(ContainerInterface $container)
	{
		
	}

//put your code here
}
