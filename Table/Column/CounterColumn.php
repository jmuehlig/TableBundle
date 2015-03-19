<?php

namespace JGM\TableBundle\Table\Column;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JGM\TableBundle\Table\Row\Row;

/**
 * Shows the row-counter.
 *
 * @author Jan Mühlig
 */
class CounterColumn extends AbstractColumn
{	
	public function getContent(Row $row)
	{
		return $row->getCount();
	}
}
