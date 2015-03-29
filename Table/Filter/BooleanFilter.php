<?php

namespace JGM\TableBundle\Table\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A boolean filter, which will contain two valid values:
 * true or false.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class BooleanFilter extends AbstractValuedFilter
{
	public function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		parent::setDefaultFilterOptions($optionsResolver);
	
		$optionsResolver->setDefaults(array(
			'true' => 'True',
			'false' => 'False'
		));
	}

	public function getValues()
	{
		return array(
			"1" => $this->true,
			"0" => $this->false
		);
	}
}
