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

use JGM\TableBundle\Table\Column\ColumnInterface;
use JGM\TableBundle\Table\Row\Row;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Simple ContentGrabber: Grabs the columns value for the row.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class SimpleContentGrabber implements ContentGrabberInterface, ContainerAwareInterface
{
	/**
	 * @var ContainerInterface
	 */
	protected $container;
	
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
	
	protected function getContainer()
	{
		return $this->container;
	}

	public function getContent(Row $row, ColumnInterface $column)
	{
		$field = $row->get($column->getName());
		if($field === null || $field === '')
		{
			return $column->getDefaultValue();
		}
		
		return $field;
	}
}
