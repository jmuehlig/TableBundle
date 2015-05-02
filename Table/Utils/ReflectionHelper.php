<?php

namespace JGM\TableBundle\Table\Utils;

/**
 * Helper for entity reflection.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class ReflectionHelper 
{
	public static function getPropertyOfEntity($entity, $property)
	{
		if(isset($entity->$property))
		{
			return $entity->$property;
		}
		else if(is_array($entity))
		{
			return $entity[$property];
		}
		else
		{
			$propertyName = strtoupper($property[0]) . substr($property, 1);

			$possibleGetter = array(
				'get' . $propertyName,
				'has' . $propertyName,
				'is' . $propertyName
			);

			foreach($possibleGetter as $getter)
			{
				$callable = array($entity, $getter);
				if(is_callable($callable))
				{
					return call_user_func($callable);
				}
			}
		}
		
		TableException::noSuchPorpertyOnEntity($property, $entity);
	}
}
