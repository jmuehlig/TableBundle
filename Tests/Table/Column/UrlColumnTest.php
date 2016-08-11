<?php

namespace JGM\TableBundle\Tests\Table\Column;

use JGM\TableBundle\Table\Column\UrlColumn;
use JGM\TableBundle\Table\Row\Row;
use JGM\TableBundle\Tests\Table\MockEntity;


/**
 * Test for the url column.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.4
 */
class UrlColumnTest extends \PHPUnit_Framework_TestCase
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
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'link_attr' => array('class' => 'btn')
		));
		
		$this->assertEquals('<a href="" class="btn"></a>', $column->getContent(new Row(new MockEntity(1, ''), 1)));
		$this->assertEquals('<a href="" class="btn"></a>', $column->getContent(new Row(new MockEntity(1, null), 1)));
	}
	
	protected function getColumn()
	{
		$column = new UrlColumn();
		$column->setName('content');
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'url' => 'http://tablebundle.org',
			'text' => 'Docs',
			'link_attr' => array('class' => 'btn')
		));
 		
		return $column;
	}
}
