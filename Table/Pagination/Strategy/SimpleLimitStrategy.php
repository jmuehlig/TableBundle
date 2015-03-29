<?php

namespace JGM\TableBundle\Table\Pagination\Strategy;

/**
 * Simple strategy for pagination: Show some pages at 
 * beginning, end and around the current page.
 * 
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class SimpleLimitStrategy implements StrategyInterface
{
	const BEGIN = 0;
	const END = 1;
	const BEFORE_CURRENT = 2;
	const AFTER_CURRENT = 3;
	
	public function getPages($currentPage, $totalPages, $maxPages) 
	{
		// Sections and their pointers for spread pages at the pagination.
		$sections = array( 
			self::BEGIN => 0,
			self::END => $totalPages-1,
			self::BEFORE_CURRENT => $currentPage-1,
			self::AFTER_CURRENT => $currentPage+1
		);
		
		// Array with pages which will be assigned to the pagination.
		$pages = array($currentPage);
		
		// Spread the pages.
		$i = 0;
		while(count($pages) < $maxPages)
		{
			$pointerIndex = $i++ % count($sections);
			$pointer = $sections[$pointerIndex];
			if(!in_array($pointer, $pages) && $pointer >= 0 && $pointer < $totalPages)
			{
				$pages[] = $pointer;
			}

			if($pointerIndex === self::BEGIN || $pointerIndex === self::AFTER_CURRENT)
			{
				$sections[$pointerIndex] += 1;
			}
			else if($pointerIndex === self::BEFORE_CURRENT || $pointerIndex === self::END)
			{
				$sections[$pointerIndex] -= 1;
			}
		}
		
		sort($pages);
		
		return $pages;
	}
}
