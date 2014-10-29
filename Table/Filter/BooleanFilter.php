<?php

namespace PZAD\TableBundle\Table\Filter;

use PZAD\TableBundle\Table\Renderer\RenderHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * 
 */
class BooleanFilter extends AbstractFilter
{
	public function needsFormEnviroment()
	{
		return true;
	}

	public function render(ContainerInterface $container)
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

//put your code here
}
