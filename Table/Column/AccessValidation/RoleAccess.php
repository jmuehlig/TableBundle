<?php

namespace PZAD\TableBundle\Table\Column\AccessValidation;

use Symfony\Component\Security\Core\SecurityContextInterface;

class RoleAccess implements ColumnAccessInterface
{
	/**
	 * @var array
	 */
	private $roles;
	
	public function __construct($roles)
	{
		if(is_string($roles))
		{
			$this->roles = array($roles);
		}
		else if(is_array($roles))
		{
			$this->roles = $roles;
		}
		else
		{
			$this->roles = array();
		}
	}
	
	public function isAccessGranted(SecurityContextInterface $securityContext)
	{
		foreach($this->roles as $role)
		{
			if($securityContext->isGranted($role) === false)
			{
				return false;
			}
		}
		
		return true;
	}

}
