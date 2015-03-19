<?php
namespace JGM\TableBundle\Table\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of ValuedFilter
 *
 * @author Jan
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
