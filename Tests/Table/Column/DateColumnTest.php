<?php

namespace JGM\TableBundle\Tests\Table\Column;

use JGM\TableBundle\Table\Column\DateColumn;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Tests\Table\MockEntity;


/**
 * Test for the date column.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.4
 */
class DateColumnTest extends \PHPUnit_Framework_TestCase
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
		
		$this->assertEquals('-', $column->getContent(new Row(new MockEntity(1, ''), 1)));
		$this->assertEquals('-', $column->getContent(new Row(new MockEntity(2, null), 2)));
	}
	
	public function testFormat()
	{
		$column = $this->getColumn();
		
		$time1 = new \DateTime();
		$time1->setDate(1989, 8, 13);
		$time1->setTime(13, 5, 10);
		
		$time2 = new \DateTime();
		$time2->setDate(2016, 8, 10);
		$time2->setTime(10, 15, 45);
		
		$this->assertEquals('13.08.1989 13:05:10', $column->getContent(new Row(new MockEntity(1, $time1), 1)));
		$this->assertEquals('10.08.2016 10:15:45', $column->getContent(new Row(new MockEntity(2, $time2), 2)));
	}

	protected function getColumn()
	{
		$column = new DateColumn();
		$column->setName('content');
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'format' => 'd.m.Y H:i:s',
			'empty_value' => '-'
		));
		
		return $column;
	}
}
