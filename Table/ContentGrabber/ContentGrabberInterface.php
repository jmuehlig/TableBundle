<?php

namespace PZAD\TableBundle\Table\ContentGrabber;

use PZAD\TableBundle\Table\Row\Row;
use PZAD\TableBundle\Table\Column\ColumnInterface;

/**
 * ContentGrabber interface.
 */
interface ContentGrabberInterface
{
	public function getContent(Row $row, ColumnInterface $column);
}
