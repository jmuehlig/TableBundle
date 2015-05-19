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
			'empty_value' => null,
			'property' => null,
			'glue' => ', '
		));
	}
	
	public function getContent(Row $row)
	{
		$entity = $this->getValue($row);

		if($entity === null)
		{
			return $this->options['empty_value'];
		}
		
		if(is_array($this->options['property']))
		{
			$parts = array();
			foreach($this->options['property'] as $property)
			{
				$partValue = ReflectionHelper::getPropertyOfEntity($entity, $property);
				if($partValue !== null && strlen($partValue) > 0)
				{
					$parts[] = $partValue;
				}
			}
			
			if(count($parts) > 0)
			{
				return implode($this->options['glue'], $parts);
			}
		}
		else 
		{
			if($this->options['property'] !== null)
			{
				$value = ReflectionHelper::getPropertyOfEntity($entity, $this->options['property']);
			}
			else
			{
				$value = (string) $entity;
			}
			
			if($value !== null && strlen($value) > 0)
			{
				return $value;
			}
		}
		
		return $this->options['empty_value']; 
	}
}
