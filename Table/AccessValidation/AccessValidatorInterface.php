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
 * Interface for objects which will check the 
 * access of a column/selection button/filter.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
interface AccessValidatorInterface
{
	/**
	 * Make sure, that the access for a column/selection button/filter is granted.
	 * 
	 * @param	AuthorizationCheckerInterface	Authorization checker.
	 * 
	 * @return	bool							True, if the access is granted. False otherwise.
	 */
	public function isAccessGranted(AuthorizationCheckerInterface $authorizationChecker);
}
