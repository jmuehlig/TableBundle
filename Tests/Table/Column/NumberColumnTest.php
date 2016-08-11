<?php

namespace JGM\TableBundle\Tests\Table\Column;

use JGM\TableBundle\Table\Column\NumberColumn;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Tests\Table\MockEntity;


/**
 * Test for the number column.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.4
 */
class NumberColumnTest extends \PHPUnit_Framework_TestCase
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
	
	public function testEmpty()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('-', $column->getContent(new Row(new MockEntity(1, ''), 1)));
		$this->assertEquals('-', $column->getContent(new Row(new MockEntity(1, null), 1)));
	}
	
	public function testDecimals()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('100,0', $column->getContent(new Row(new MockEntity(1, 100), 1)));
		$this->assertEquals('100,2', $column->getContent(new Row(new MockEntity(1, 100.243589), 1)));
	}
	
	public function testThousands()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('100.000.000,0', $column->getContent(new Row(new MockEntity(1, 100000000), 1)));
	}
	
	public function testString()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('-', $column->getContent(new Row(new MockEntity(1, 'abcdefg'), 1)));
	}
	
	protected function getColumn()
	{
		$column = new NumberColumn();
		$column->setName('content');
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'empty_value' => '-',
			'decimals' => '1',
			'decimal_point' => ',',
			'thousands_sep' => '.'
		));
		
		return $column;
	}
}
