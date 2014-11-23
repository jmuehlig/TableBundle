<?php

namespace PZAD\TableBundle\Table\Filter;

use PZAD\TableBundle\Table\Renderer\RenderHelper;

/**
 * Simple filter for filtering text.
 *
 * @author	Jan Mühlig <mail@jamuehlig.de>
 * @since	1.0.0
 */
class TextFilter extends AbstractFilter
{
	public function needsFormEnviroment()
	{
		return true;
	}

	public function render()
	{
		$value = "";
		if($this->getValue() !== null)
		{
			$value = sprintf(" value=\"%s\"", $this->getValue());
		}
		return sprintf("<input name=\"%s\"%s%s />", $this->getName(), $value, RenderHelper::attrToString($this->getAttributes()));
	}
}
