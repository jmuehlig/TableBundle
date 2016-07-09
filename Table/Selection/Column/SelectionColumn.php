<?php

namespace JGM\TableBundle\Table\Selection\Column;

use JGM\TableBundle\Table\Column\AbstractColumn;
use JGM\TableBundle\Table\Row\Row;

/**
 */
class SelectionColumn extends AbstractColumn
{
	const NAME_PREFIX = 'selection_';
	
	public function __construct($tableName)
	{
		$this->setName(self::NAME_PREFIX . $tableName);
		$this->setOptions(array(
			'label' => ''
		));
	}
	
	public function isSortable()
	{
		return false;
	}
	
	public function getContent(Row $row)
	{
		return sprintf('<input type="checkbox" value="%s" name="%s[]" />', $row->get('id'), $this->getName());
	}
}
