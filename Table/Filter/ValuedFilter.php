<?php
namespace PZAD\TableBundle\Table\Filter;

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
		$optionsResolver->setAllowedTypes(array(
			'values' => 'array'
		));
		
		parent::setDefaultFilterOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'values' => array()
		));
	}
	
	public function getValues()
	{
		return $this->values;
	}
}
