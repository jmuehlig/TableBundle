<?php

namespace JGM\TableBundle\Tests\Table\Column;

use JGM\TableBundle\Table\Column\TextColumn;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Tests\Table\MockEntity;


/**
 * Test for the text column.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.4
 */
class TextColumnTest extends \PHPUnit_Framework_TestCase
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
	
	public function testMaxlength()
	{
		$column = $this->getColumn();
		
		$this->assertEquals('lorem', $column->getContent(new Row(new MockEntity(1, 'lorem'), 1)));
		$this->assertEquals('lorem?', $column->getContent(new Row(new MockEntity(1, 'lorem ipsum'), 1)));
	}
	
	protected function getColumn()
	{
		$column = new TextColumn();
		$column->setName('content');
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'empty_value' => '-',
			'maxlength' => '5',
			'after_maxlength' => '?',
			'nl2br' => true
		));
 		
		return $column;
	}
}
