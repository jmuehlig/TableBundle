<?php

namespace JGM\TableBundle\Table\Column;

use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Table\Utils\ReflectionHelper;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * This column will call the __toString method of an entity.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class EntityColumn extends AbstractColumn
{
	protected function setDefaultOptions(OptionsResolverInterface $optionsResolver)
	{
		parent::setDefaultOptions($optionsResolver);
		
		$optionsResolver->setDefaults(array(
			'empty_value' => null
		));
	}
	
	public function getContent(Row $row)
	{
		$properties = explode(".", $this->getName());
		$value = $row->get($properties[0]);
		for($i = 1; $i < count($properties); $i++)
		{
			if($value === null)
			{
				return $this->options['empty_value'];
			}
			
			$value = ReflectionHelper::getPropertyOfEntity($value, $properties[$i]);
		}

		if($value === null)
		{
			return $this->options['empty_value'];
		}
		
		return $value->__toString();
	}
}
