<?php

namespace JGM\TableBundle\Table\Column\ContentGrabber;

use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Table\Column\ColumnInterface;

/**
 * ContentGrabber interface.
 * A content grabber can create content for
 * a table cell.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
interface ContentGrabberInterface
{
	public function getContent(Row $row, ColumnInterface $column);
}
