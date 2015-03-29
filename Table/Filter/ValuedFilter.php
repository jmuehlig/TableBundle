<?php
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
