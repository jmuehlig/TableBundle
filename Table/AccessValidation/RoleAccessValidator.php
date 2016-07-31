<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\AccessValidation;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Access validation, which get the accessibility of 
 * the user by his role(s).
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
class RoleAccessValidator implements AccessValidatorInterface
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
	
	public function isAccessGranted(AuthorizationCheckerInterface $checker)
	{
		foreach($this->roles as $role)
		{
			if($checker->isGranted($role) === false)
			{
				return false;
			}
		}
		
		return true;
	}

}
