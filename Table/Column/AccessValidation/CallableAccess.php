<?php

namespace JGM\TableBundle\Table\Column\AccessValidation;

use Symfony\Component\Security\Core\SecurityContextInterface;

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
