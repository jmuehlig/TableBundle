<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Filter\ValueManipulator;

/**
 * Value Manipulator Interface, which can manipulate a filter given value.
 */
interface ValueManipulatorInterface
{
	public function getValue($originalValue);
}
