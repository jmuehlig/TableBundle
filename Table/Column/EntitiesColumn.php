<?php

namespace JGM\TableBundle\Table\Column;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JGM\TableBundle\Table\Row\Row;

/**
 * This column will call the __toString method of each entity in the entities array.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class EntitiesColumn extends AbstractColumn
{
	protected function setDefaultOptions(OptionsResolverInterface $optionsResolver)
	{
		parent::setDefaultOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'empty_value' => null,
			'glue' => ', '
		));
	}
	
	public function getContent(Row $row)
	{
		$values = $row->get($this->getName());
		
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
