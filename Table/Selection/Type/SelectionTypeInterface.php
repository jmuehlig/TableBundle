<?php

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
