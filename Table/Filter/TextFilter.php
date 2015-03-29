<?php

namespace JGM\TableBundle\Table\Filter;

use JGM\TableBundle\Table\Renderer\RenderHelper;

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

	public function getWidgetBlockName()
	{
		return 'text_widget';
	}
}
