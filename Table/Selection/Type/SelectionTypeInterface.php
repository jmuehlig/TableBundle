<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JGM\TableBundle\Table\Selection\Type;

use JGM\TableBundle\Table\Selection\SelectionButtonBuilder;

/**
 * Interface for table types, which want to use
 * any kind of selection.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.3
 */
interface SelectionTypeInterface
{
	public function buildSelectionButtons(SelectionButtonBuilder $selectionButtonBuilder);
}
