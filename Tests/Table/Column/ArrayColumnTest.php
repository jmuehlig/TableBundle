<?php

namespace JGM\TableBundle\Tests\Table\Column;

use JGM\TableBundle\Table\Column\ArrayColumn;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Tests\Table\MockEntity;


/**
 * Test for the array column.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.4
 */
class ArrayColumnTest extends \PHPUnit_Framework_TestCase
{
	public function testName()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('content', $column->getName());
	}
	
	public function testOptions()
	{
		$column = $this->getColumn();
		
		$this->assertEquals(array('class' => 'col'), $column->getAttributes());
		$this->assertEquals(array('class' => 'col-head'), $column->getHeadAttributes());
		$this->assertFalse($column->isSortable());
		$this->assertEquals('Test Column', $column->getLabel());
	}
	
	public function testEmptyRow()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('-', $column->getContent(new Row(new MockEntity(1, null), 1)));
		$this->assertEquals('-', $column->getContent(new Row(new MockEntity(2, array()), 1)));
	}
	
	public function testOneItem()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('Lorem', $column->getContent(new Row(new MockEntity(1, array('Lorem')), 1)));
	}
	
	public function testManyItems()
	{
		$column = $this->getColumn();
		
		$this->assertEquals(
			'Lorem ipsum dolor sit', 
			$column->getContent(new Row(new MockEntity(1, array(
				'Lorem', 'ipsum', 'dolor', 'sit'
			)), 1))
		);
	}
	
	/**
	 * @expectedException \JGM\TableBundle\Table\TableException
	 */
	public function testNoArray()
	{
		$column = $this->getColumn();
		
		$column->getContent(new Row(new MockEntity(1, 'lorem ipsum'), 1));
	}
	
	protected function getColumn()
	{
		$column = new ArrayColumn();
		$column->setName('content');
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'empty_value' => '-',
			'glue' => ' '
		));
		
		return $column;
	}
}
