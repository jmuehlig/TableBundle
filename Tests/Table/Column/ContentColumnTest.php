<?php

namespace JGM\TableBundle\Tests\Table\Column;

use JGM\TableBundle\Table\Column\ColumnInterface;
use JGM\TableBundle\Table\Column\ContentColumn;
use JGM\TableBundle\Table\Column\ContentGrabber\SimpleContentGrabber;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Table\TableException;
use JGM\TableBundle\Tests\Table\MockEntity;


/**
 * Test for the content column.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.4
 */
class ContentColumnTest extends \PHPUnit_Framework_TestCase
{
	public function testName()
	{
		$column = $this->getColumn(new SimpleContentGrabber());
		
		$this->assertEquals('content', $column->getName());
	}
	
	public function testOptions()
	{
		$column = $this->getColumn(new SimpleContentGrabber());
		
		$this->assertEquals(array('class' => 'col'), $column->getAttributes());
		$this->assertEquals(array('class' => 'col-head'), $column->getHeadAttributes());
		$this->assertTrue($column->isSortable());
		$this->assertEquals('Test Column', $column->getLabel());
	}
	
	/**
	 * @expectedException JGM\TableBundle\Table\TableException
	 */
	public function testWithoutContentGrabber()
	{
		$column = $this->getColumn();
		$column->getContent(new Row(new MockEntity(1, ''), 1));
	}
	
	public function testWithFunction()
	{
		$column = $this->getColumn(function(Row $row, ColumnInterface $column) {
			$this->assertEquals('content', $column->getName());
			return 'Lorem ipsum';
		});
		$this->assertEquals(
			'Lorem ipsum',
			$column->getContent(new Row(new MockEntity(1, 'test'), 1))
		);
	}
	
	public function testWithClass()
	{
		$column = $this->getColumn(new SimpleContentGrabber());
		$this->assertEquals(
			'test',
			$column->getContent(new Row(new MockEntity(1, 'test'), 1))
		);
	}
	
	protected function getColumn($contentGrabber = null)
	{
		$column = new ContentColumn();
		$column->setName('content');
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'content_grabber' => $contentGrabber
		));
 		
		return $column;
	}
}
