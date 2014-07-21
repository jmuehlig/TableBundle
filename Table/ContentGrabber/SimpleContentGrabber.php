<?php

namespace PZAD\TableBundle\Table\ContentGrabber;

use PZAD\TableBundle\Table\Row\Row;
use PZAD\TableBundle\Table\Column\ColumnInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Simple ContentGrabber: Grabs the columns value for the row.
 */
class SimpleContentGrabber implements ContentGrabberInterface
{
	/**
	 * @var ContainerInterface
	 */
	protected $_container;
	
	public function setContainer(ContainerInterface $container)
	{
		$this->_container = $container;
	}
	
	protected function getContainer()
	{
		return $this->_container;
	}

	public function getContent(Row $row, ColumnInterface $column)
	{
		$field = $row->get($column->getName());
		if($field == null || $field == '')
		{
			return $column->getDefaultValue();
		}
		
		return $field;
	}
}
