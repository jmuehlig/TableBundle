<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Utils;

use JGM\TableBundle\Table\TableException;

/**
 * Helper for entity reflection.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class ReflectionHelper 
{
	/**
	 * Cache for property getter function names.
	 * 
	 * @var array
	 */
	private static $propertyGetterFunctionCache = array();
	
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
			$callable = self::getGetterNameOfProperty($entity, $property);
			if($callable !== null)
			{
				return call_user_func($callable);
			}
		}
		
		TableException::noSuchPropertyOnEntity(null, $property, $entity);
	}
	
	/**
	 * Finds the name of the getter of a property of an entity and
	 * returns its callable.
	 * 
	 * @param object $entity	Entity.
	 * @param string $property	Name of the property.
	 * @return callable
	 */
	private static function getGetterNameOfProperty($entity, $property)
	{
		$entityClassName = get_class($entity);
		if(!array_key_exists($entityClassName, self::$propertyGetterFunctionCache))
		{
			self::$propertyGetterFunctionCache[$entityClassName] = array();
		}
		
		if(!array_key_exists($property, self::$propertyGetterFunctionCache[$entityClassName]))
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
					self::$propertyGetterFunctionCache[$entityClassName][$property] = $getter;
					break;
				}
			}
		}
		
		if(array_key_exists($property, self::$propertyGetterFunctionCache[$entityClassName]))
		{
			return array($entity, self::$propertyGetterFunctionCache[$entityClassName][$property]);
		}
		
		return null;
	}
}
