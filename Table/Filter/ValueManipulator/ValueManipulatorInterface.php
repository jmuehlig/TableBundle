<?php
namespace JGM\TableBundle\Table\Filter\ValueManipulator;

use JGM\TableBundle\Table\Filter\FilterInterface;

/**
 * Value Manipulator Interface, which can manipulate a filter given value.
 */
interface ValueManipulatorInterface
{
	public function getValue($originalValue);
}
