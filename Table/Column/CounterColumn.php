<?php

namespace JGM\TableBundle\Table\Column;

use JGM\TableBundle\Table\Row\Row;

/**
 * Shows the row-counter.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class CounterColumn extends AbstractColumn
{	
	public function getContent(Row $row)
	{
		return $row->getCount();
	}
}
