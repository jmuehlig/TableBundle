<?php

namespace JGM\TableBundle\Tests\Table\Column\ContentGrabber;

use JGM\TableBundle\Table\Column\ContentGrabber\SimpleContentGrabber;
use JGM\TableBundle\Table\Column\TextColumn;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Tests\Table\MockEntity;

/**
 * Test for the simple content grabber.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.4
 */
class SimpleContentGrabberTest extends \PHPUnit_Framework_TestCase
{
	public function testEmpty()
	{
		$contentGrabber = new SimpleContentGrabber();
		
		$column = $this->getColumn();
		
		$this->assertEquals('-', $contentGrabber->getContent(
			new Row(new MockEntity(1, ''), 1),
			$column
		));
		$this->assertEquals('-', $contentGrabber->getContent(
			new Row(new MockEntity(2, null), 2),
			$column
		));
	}
	
	public function testContent()
	{
		$contentGrabber = new SimpleContentGrabber();
		
		$column = $this->getColumn();
		
		$this->assertEquals('0', $contentGrabber->getContent(
			new Row(new MockEntity(1, '0'), 1),
			$column
		));
		$this->assertEquals('Lorem ipsum', $contentGrabber->getContent(
			new Row(new MockEntity(2, 'Lorem ipsum'), 2),
			$column
		));
	}
	
	protected function getColumn()
	{
		$column = new TextColumn();
		$column->setName('content');
		$column->setOptions(array(
			'empty_value' => '-',
		));
 		
		return $column;
	}
}
