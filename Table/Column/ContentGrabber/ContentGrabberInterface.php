<?php

namespace JGM\TableBundle\Table\Column\ContentGrabber;

use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Table\Column\ColumnInterface;

/**
 * ContentGrabber interface.
 */
interface ContentGrabberInterface
{
	public function getContent(Row $row, ColumnInterface $column);
}
