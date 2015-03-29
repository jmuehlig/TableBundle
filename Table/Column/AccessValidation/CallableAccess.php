<?php

namespace JGM\TableBundle\Table\Column\AccessValidation;

use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Access validation, which can execute an anonymous 
 * function get the users access.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class CallableAccess implements ColumnAccessInterface
{
	/**
	 * @var callable
	 */
	private $callable;
	
	public function __construct($callable)
	{
		$this->callable = $callable;
	}
	
	public function isAccessGranted(SecurityContextInterface $securityContext)
	{
		if(is_callable($this->callable))
		{
			return call_user_func($this->callable, $securityContext);
		}
		
		return true;
	}
}
