<?php

namespace JGM\TableBundle\Tests\Table;

/**
 * Entity for testing.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.4
 */
class MockEntity
{
	/**
	 * @var int
	 */
	private $id;
	
	/**
	 * @var mixed
	 */
	private $content;
	
	public function __construct($id = 1, $content = null)
	{
		$this->id = $id;
		$this->content = $content;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getContent()
	{
		return $this->content;
	}
}
