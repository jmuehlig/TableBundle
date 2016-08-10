<?php

namespace JGM\TableBundle\Tests\Table\Column;

use JGM\TableBundle\Table\Column\BooleanColumn;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Tests\Table\MockEntity;


/**
 * Test for the boolean column.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.4
 */
class BooleanColumnTest extends \PHPUnit_Framework_TestCase
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
		$this->assertTrue($column->isSortable());
		$this->assertEquals('Test Column', $column->getLabel());
	}
	
	public function testEmptyRow()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('N', $column->getContent(new Row(new MockEntity(1, null), 1)));
	}
	
	public function testFalse()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('N', $column->getContent(new Row(new MockEntity(1, false), 1)));
	}
	
	public function testNotTrue()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('N', $column->getContent(new Row(new MockEntity(1, 'lorem ipsum'), 1)));
		$this->assertEquals('N', $column->getContent(new Row(new MockEntity(2, '0'), 2)));
		$this->assertEquals('N', $column->getContent(new Row(new MockEntity(3, null), 3)));
	}
	
	public function testTrue()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('Y', $column->getContent(new Row(new MockEntity(1, '1'), 1)));
		$this->assertEquals('Y', $column->getContent(new Row(new MockEntity(2, 1), 2)));
		$this->assertEquals('Y', $column->getContent(new Row(new MockEntity(3, true), 3)));
	}

	protected function getColumn()
	{
		$column = new BooleanColumn();
		$column->setName('content');
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'true' => 'Y',
			'false' => 'N'
		));
		
		return $column;
	}
}
