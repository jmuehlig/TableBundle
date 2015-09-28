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
 * Showing all pages.
 *
 * @author	Jan Mühlig <mail@janmuehlig.de>
 * @since	1.0
 */
class AllPagesStrategy implements StrategyInterface
{
	public function getPages($currentPage, $totalPages, $maxPages) 
	{
		$pages = array();
		for($i = 0; $i < $totalPages; $i++)
		{
			$pages[] = $i;
		}
		
		return $pages;
	}
}
