<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
