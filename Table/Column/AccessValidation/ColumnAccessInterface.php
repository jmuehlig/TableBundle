<?php

namespace JGM\TableBundle\Table\Column\AccessValidation;

use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Interface for objects which will check the access of a column.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
interface ColumnAccessInterface
{
	/**
	 * Make sure, that the access for a column is granted.
	 * 
	 * @param	SecurityContextInterface	Security context.
	 * 
	 * @return	bool						True, if the access is granted. False otherwise.
	 */
	public function isAccessGranted(SecurityContextInterface $securityContext);
}
