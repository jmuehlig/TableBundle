<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Simple filter for holding a set of values, defined
 * by the user.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class ValuedFilter extends AbstractValuedFilter
{
	public function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		parent::setDefaultFilterOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'values' => array()
		));
		
		$optionsResolver->setAllowedTypes(array(
			'values' => 'array'
		));
	}
	
	public function getValues()
	{
		return $this->values;
	}
}
