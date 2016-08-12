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
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column'
		));
		
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
			'link_attr' => array('class' => 'btn'),
			'empty_value' => '-'
		));
		
		$this->assertEquals('-', $column->getContent(new Row(new MockEntity(1, ''), 1)));
		$this->assertEquals('-', $column->getContent(new Row(new MockEntity(1, null), 1)));
	}
	
	public function testFixUrlWithDynamicText()
	{
		$column = $this->getColumn();
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'url' => 'http://tablebundle.org',
		));
		
		$this->assertEquals(
			'<a href="http://tablebundle.org">Lorem ipsum</a>',
			$column->getContent(new Row(
				new MockEntity(1, 'Lorem ipsum'),
				1
			))
		);
	}
	
	public function testFixUrlWithFixText()
	{
		$column = $this->getColumn();
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'url' => 'http://tablebundle.org',
			'text' => 'Doc'
		));
		
		$this->assertEquals(
			'<a href="http://tablebundle.org">Doc</a>',
			$column->getContent(new Row(
				new MockEntity(1, 'Lorem ipsum'),
				1
			))
		);
	}
	
//	public function testRouteWithDynamicText()
//	{
//	}
//	
//	public function testRouteWithFixText()
//	{
//	}
	
	public function testDynamicUrlWithDynamicText()
	{
		$column = $this->getColumn();
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column'
		));
		
		$this->assertEquals(
			'<a href="http://localhost/4.de">http://localhost/4.de</a>',
			$column->getContent(new Row(
				new MockEntity(4, 'http://localhost/{{id}}.de'),
				1
			))
		);
	}
	
	public function testDynamicUrlWithFixText()
	{
		$column = $this->getColumn();
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'text' => 'Details'
		));
		
		$this->assertEquals(
			'<a href="http://localhost/5.de">Details</a>',
			$column->getContent(new Row(
				new MockEntity(5, 'http://localhost/{{id}}.de'),
				1
			))
		);
	}
	
	public function testLinkAttributes()
	{
		$column = $this->getColumn();
		$column->setOptions(array(
			'attr' => array('class' => 'col'),
			'head_attr' => array('class' => 'col-head'),
			'sortable' => true,
			'label' => 'Test Column',
			'url' => 'http://tablebundle.org',
			'link_attr' => array('class' => 'btn btn-primary')
		));
		
		$this->assertEquals(
			'<a href="http://tablebundle.org" class="btn btn-primary">Lorem ipsum</a>',
			$column->getContent(new Row(
				new MockEntity(1, 'Lorem ipsum'),
				1
			))
		);
	}
	
	protected function getColumn()
	{
		$column = new UrlColumn();
		$column->setName('content');
 		
		return $column;
	}
}
