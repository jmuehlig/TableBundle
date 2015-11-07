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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This column will list all items of an array, seperated
 * by a given glue.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class ArrayColumn extends AbstractColumn
{
	protected function configureOptions(OptionsResolver $optionsResolver)
	{
		parent::configureOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'empty_value' => null,
			'glue' => ', '
		));
	}
	
	public function getContent(Row $row)
	{
		$values = $this->getValue($row);
		
		if($values === null)
		{
			return $this->options['empty_value'];
		}
		
		$valueStrings = [];
		foreach($values as $value)
		{
			$valueStrings[] = (string) $value;
		}
		
		return implode($this->options['glue'], $valueStrings);
	}
	
	public function isSortable()
	{
		return false;
	}
}
