<?php

namespace PZAD\TableBundle\Table;

/**
 * Helper for the table bundle.
 */
class Utils
{
	/**
	 * Finds the option in the options-array, identified by name.
	 * 
	 * @param string $name			Name of the option.
	 * @param array $options		Array of options.
	 * @param mixed $defaultValue	Default value, default NULL.
	 */
	public static function getOption($name, $options, $defaultValue = null)
	{
		if(!is_array($options) || !array_key_exists($name, $options))
		{
			return $defaultValue;
		}
		
		return $options[$name];
	}
	
	/**
	 * Renders an array of attributes.
	 * 
	 * @param array $attributes Array of attributes.
	 * 
	 * @return string HTML Code of rendered attributes array.
	 */
	public static function renderAttributesContent($attributes)
	{
		if(!is_array($attributes))
		{
			return "";
		}
		
		$content = "";
		foreach($attributes as $attributeName => $attributeValue)
		{
			$content .= sprintf(" %s=\"%s\"", $attributeName, $attributeValue);
		}
		
		return $content;
	}
}
