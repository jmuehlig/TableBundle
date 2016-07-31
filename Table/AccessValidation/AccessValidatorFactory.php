<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace JGM\TableBundle\Table\AccessValidation;

/**
 * Factory for creating the right validator,
 * depending on the developers access option.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class AccessValidatorFactory
{
	public static function getValidator($optionValue)
	{
		if($optionValue instanceof AccessValidatorInterface)
		{
			return $optionValue;
		}
		
		if(is_callable($optionValue))
		{
			return new CallableAccessValidator($optionValue);
		}
		
		if(is_string($optionValue) || is_array($optionValue))
		{
			return new RoleAccessValidator($optionValue);
		}
		
		return null;
	}
}
