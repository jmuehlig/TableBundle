<?php

namespace JGM\TableBundle\Table\Renderer;

/**
 * Description of RenderHelper
 *
 * @author Jan
 */
class RenderHelper
{
	public static function attrToString(array $attr)
	{
		if(count($attr) < 1)
		{
			return "";
		}
		
		$parts = array();
		foreach($attr as $name => $value)
		{
			$parts[] = sprintf(" %s=\"%s\"", $name, $value);
		}
		
		return implode(" ", $parts);
	}
}
