<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
	public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver)
	{
		parent::configureOptions($optionsResolver);
		
		$optionsResolver->setDefault('prefix', '');
	}
	
	public function getContent(Row $row)
	{
		return $this->options['prefix'] . $row->getCount();
	}
}
