<?php

namespace PZAD\TableBundle\Table\Filter;

use PZAD\TableBundle\Table\Renderer\RenderHelper;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * 
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
	public function renderCheckbox()
	{
		$attr = $this->getAttributes();
		if($this->getValue() == 1)
		{
			$attr['checked'] = 'checked';
		}
		
		return sprintf(
			"<input type=\"checkbox\" id=\"%s\%s />",
			$this->getName(),
			RenderHelper::attrToString($attr)
		);
	}

	protected function getValues()
	{
		return array(
			1 => $this->true,
			0 => $this->false
		);
	}
}
