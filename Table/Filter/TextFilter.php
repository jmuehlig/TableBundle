<?php

namespace PZAD\TableBundle\Table\Filter;

use PZAD\TableBundle\Table\Renderer\RenderHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Simple filter for filtering text.
 *
 * @author	Jan Mühlig <mail@jamuehlig.de>
 * @since	1.0.0
 */
class TextFilter extends AbstractFilter
{
	protected function setDefaultFilterOptions(OptionsResolver $optionsResolver)
	{
		parent::setDefaultFilterOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'placeholder' => null
		));
	}

	public function needsFormEnviroment()
	{
		return true;
	}

	public function render(ContainerInterface $container)
	{
		$placeholder = $this->getPlaceholder() !== null ? sprintf(" placeholder=\"%s\"", $this->getPlaceholder()) : "";
		
		return sprintf("<input name=\"%s\"%s%s />", $this->getName(), RenderHelper::attrToString($this->getAttributes()), $placeholder);
	}
}
