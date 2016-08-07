<?php

/*
 * This file is part of the TableBundle.
 *
 * (c) Jan Mühlig <mail@janmuehlig.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
		if($maxPages === null || $totalPages <= $maxPages)
		{
			return range(0, $totalPages - 1);
		}
            
		// Sections and their pointers for spread pages at the pagination.
		$sections = array( 
			self::BEGIN => 0,
			self::END => $totalPages-1,
			self::BEFORE_CURRENT => $currentPage-1,
			self::AFTER_CURRENT => $currentPage+1
		);
                
		$countSections = count($sections);
		
		// Array with pages which will be assigned to the pagination.
		$pages = array($currentPage);
                
		for($i = 0; $i < $maxPages - 1; $i++)
		{
			$sectionIndex = $i % $countSections;
			$page = $sections[$sectionIndex];
			if($page < 0)
			{
				$sectionIndex += 1;
				$page = $sections[$sectionIndex];
			}
			else if($page > $totalPages - 1)
			{
				$sectionIndex -= 1;
				$page = $sections[$sectionIndex];
			}
			
			$pages[] = $page;

			$multiplier = 1;
			if($sectionIndex === self::BEFORE_CURRENT || $sectionIndex === self::END)
			{
				$multiplier = -1;
			}

			$sections[$sectionIndex] += $multiplier;
		}

		$uniquePages = array_unique($pages, SORT_NUMERIC);
		
		sort($uniquePages, SORT_NUMERIC);
		
		return $uniquePages;
	}
}
