<?php

namespace JGM\TableBundle\Tests\Table\Pagination\Strategy;

use JGM\TableBundle\Table\Pagination\Strategy\SimpleLimitStrategy;

/**
 * Test for the SimpleLimitStrategy.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.4
 */
class SimpleLimitStrategyTest extends \PHPUnit_Framework_TestCase
{
	public function testNoMaxPages()
	{
		$strategy = new SimpleLimitStrategy();
		
		$this->assertEquals
		(
			range(0, 9),
			$strategy->getPages(0, 10, null)
		);
	}
	
	public function testNotUsedMaxPage()
	{
		$strategy = new SimpleLimitStrategy();
		
		$this->assertEquals
		(
			range(0, 9),
			$strategy->getPages(0, 10, 11)
		);
	}
	
	public function testWithCurrentFirst()
	{
		$strategy = new SimpleLimitStrategy();
		
		$this->assertEquals
		(
			array(0, 1, 2, 9),
			$strategy->getPages(0, 10, 5)
		);
	}
	
	public function testWithCurrentLast()
	{
		$strategy = new SimpleLimitStrategy();
		
		$this->assertEquals
		(
			array(0, 7, 8, 9),
			$strategy->getPages(9, 10, 5)
		);
	}
}
