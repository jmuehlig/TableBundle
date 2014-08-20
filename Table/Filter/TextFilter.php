<?php

namespace PZAD\TableBundle\Table\Filter;

/**
 * Simple filter for filtering text.
 *
 * @author	Jan Mühlig <mail@jamuehlig.de>
 * @since	1.0.0
 */
class TextFilter extends AbstractFormFilter
{
	protected function getType()
	{
		return 'text';
	}
}
