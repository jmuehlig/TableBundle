<?php

namespace JGM\TableBundle\Tests\Table\Column;

use JGM\TableBundle\Table\Column\CounterColumn;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Tests\Table\MockEntity;


/**
 * Test for the counter column.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.4
 */
class CounterColumnTest extends \PHPUnit_Framework_TestCase
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
	
	public function testContent()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('#1', $column->getContent(new Row(new MockEntity(4, 'lorem ipsum'), 1)));
		$this->assertEquals('#3', $column->getContent(new Row(new MockEntity(5, 'lorem ipsum'), 3)));
		$this->assertEquals('#2', $column->getContent(new Row(new MockEntity(6, 'lorem ipsum'), 2)));
	}

	protected function getColumn()
	{
		$column = new CounterColumn();
		$column->setName('content');
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'prefix' => '#'
		));
		
		return $column;
	}
}
