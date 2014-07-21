<?php

namespace PZAD\TableBundle\Table\Column;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PZAD\TableBundle\Table\Row\Row;

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
